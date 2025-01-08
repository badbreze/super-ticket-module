<?php

namespace super\ticket\models;

use elitedivision\amos\attachments\behaviors\FileBehavior;
use elitedivision\amos\attachments\models\File;
use super\ticket\db\ActiveRecord;
use Yii;
use yii\base\Exception;
use super\ticket\mail\Mailer;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%super_ticket_event}}".
 *
 * @property int $id
 * @property string $type Type of Event
 * @property string $metadata
 * @property string $body
 * @property int $ticket_id
 * @property int $super_user_id
 * @property string|null $created_at Creato il
 * @property string|null $updated_at Aggiornato il
 * @property string|null $deleted_at Cancellato il
 * @property int|null $created_by Creato da
 * @property int|null $updated_by Aggiornato da
 * @property int|null $deleted_by Cancellato da
 * @property SuperUser|null $creator
 *
 * @property SuperTicket $ticket
 * @property SuperUser $superUser
 * @property File[] $attachments
 */
class SuperTicketEvent extends ActiveRecord
{
    const TYPE_STATUS_CHANGE = 'status';
    const TYPE_PRIORITY = 'priority';
    const TYPE_COMMENT = 'comment';
    const TYPE_ASSIGNEE = 'assignee';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%super_ticket_event}}';
    }

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'fileBehavior' => [
                'class' => FileBehavior::className()
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'ticket_id'], 'required'],
            [['metadata', 'body'], 'string'],
            [['ticket_id', 'super_user_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['type'], 'string', 'max' => 255],
            [
                ['ticket_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => SuperTicket::className(),
                'targetAttribute' => ['ticket_id' => 'id']
            ],
            [
                ['super_user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => SuperUser::className(),
                'targetAttribute' => ['super_user_id' => 'id']
            ],
            [['attachments'], 'file', 'maxFiles' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('super', 'ID'),
            'type' => Yii::t('super', 'Type of Event'),
            'metadata' => Yii::t('super', 'Metadata'),
            'body' => Yii::t('super', 'Body'),
            'ticket_id' => Yii::t('super', 'Ticket ID'),
            'super_user_id' => Yii::t('super', 'Super User ID'),
            'created_at' => Yii::t('super', 'Created At'),
            'updated_at' => Yii::t('super', 'Updated At'),
            'deleted_at' => Yii::t('super', 'Deleted At'),
            'created_by' => Yii::t('super', 'Created By'),
            'updated_by' => Yii::t('super', 'Updated By'),
            'deleted_by' => Yii::t('super', 'Deleted By'),
        ];
    }

    /**
     * Gets query for [[Ticket]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTicket()
    {
        return $this->hasOne(SuperTicket::className(), ['id' => 'ticket_id']);
    }

    /**
     * Gets query for [[Ticket]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSuperUser()
    {
        return $this->hasOne(SuperUser::className(), ['id' => 'super_user_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(SuperUser::class, ['user_id' => 'created_by']);
    }

    public static function createTicketEvent($ticket_id, $type, $body, $super_user_id = null, $metadata = null)
    {
        if (empty($super_user_id)) {
            $superUser = SuperUser::findOne(['user_id' => Yii::$app->user->id]);

            $super_user_id = $superUser->id;
        }

        $event = new SuperTicketEvent();
        $event->ticket_id = $ticket_id;
        $event->super_user_id = $super_user_id;
        $event->type = $type;
        $event->body = $body;
        $event->metadata = json_encode($metadata);

        if ($event->save()) {
            SuperTicketFollower::follow($ticket_id, $super_user_id);

            //TODO questa cosa della riapertura va riorganizzata in modo da avere codice più pulito
            if ($event->type == self::TYPE_COMMENT) {
                if ($event->ticket->status->identifier == SuperTicketStatus::STATUS_RESOLVED) {
                    //TODO va resettata la SLA come richiesto da marco
                    $event->ticket->changeStatus(SuperTicketStatus::STATUS_OPEN);
                }
            }

            if (in_array($type, [self::TYPE_COMMENT/*, self::TYPE_STATUS_CHANGE*/])) {
                $event->sendNotification();
            }

            return $event;
        } else {
            throw new \Exception('Invalid Event Registration');
        }

        //VOID
    }

    public function sendNotification()
    {
        $domainMailer = $this->ticket->domain->mailer;

        if ($domainMailer && $domainMailer->enabled) {
            $mailer = new Mailer([
                'useFileTransport' => false,
                'transport' => [
                    'class' => 'Swift_SmtpTransport',
                    'host' => $domainMailer->host,
                    'username' => $domainMailer->username,
                    'password' => $domainMailer->password,
                    'port' => $domainMailer->port,
                    'encryption' => $domainMailer->encryption,
                ],
                'messageConfig' => [
                    'priority' => 3    // 1 MAX 3 NORMAL 5 LOWER
                ],
            ]);
        } else {
            throw new Exception('Mailer Not Configured for this Domain');
        }

        //Override Layout for template usage
        $mailer->htmlLayout = "@vendor/badbreze/super-ticket-system/src/views/mail/layouts/html";

        $recipients = $this->getRecipients();
        $mainRecipient = reset($recipients);
        unset($recipients[0]);

        $composition = $mailer
            ->compose("@vendor/badbreze/super-ticket-system/src/views/mail/{$this->type}", [
                'event' => $this
            ])
            ->setFrom($domainMailer->from ?: 'no-reply@super.ticket');

        if ($mainRecipient) {
            $composition->setTo($mainRecipient->superUser->email);
        } else {
            Yii::$app->session->addFlash('error', Yii::t('super', 'No Recipients For Notification'));
            return false;
        }

        $composition
            ->setCc(ArrayHelper::map($recipients, 'superUser.email', 'superUser.fullName'))
            ->setSubject($this->getNotificationSubject());

        return $composition->send();
    }

    //TODO da finalizzare, scritto così è nammerda
    public function getNotificationSubject()
    {
        $subject = "[#T{$this->ticket_id}] - ";
        $subject .= Yii::t('super', "ticket_activity_{$this->type}", [
            'id' => $this->ticket_id,
            'subject' => $this->ticket->subject,
            'type' => $this->type,
        ]);

        return $subject;
    }

    public function getRecipients()
    {
        //metadata used for special functions like custom recipients
        $metadata = !empty($this->metadata) ? json_decode($this->metadata, true) : [];

        $exclusions = [$this->super_user_id];

        if (!empty($metadata) && isset($metadata['recipients'])) {
            return SuperTicketFollower::find()
                ->andWhere(['ticket_id' => $this->ticket_id])
                ->andWhere(['super_user_id' => $metadata['recipients']])
                ->andWhere(['not', ['super_user_id' => [$this->super_user_id]]])
                //->andWhere(['status' => SuperTicketFollower::STATUS_FOLLOW])
                ->all();
        }

        return $this->ticket->getFollowers($exclusions)->all();
    }

    public function getPrevious($type = 'comment')
    {
        $query = self::find()
            ->andWhere(['ticket_id' => $this->ticket_id])
            ->andWhere(['<', 'id', $this->id])
            ->orderBy(['id' => SORT_DESC]);

        if ($type) {
            $query->andWhere(['type' => $type]);
        }

        return $query->one();
    }
}

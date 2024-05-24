<?php

namespace super\ticket\models;

use super\ticket\db\ActiveRecord;
use Yii;
use yii\base\Exception;
use super\ticket\mail\Mailer;

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
 * @property yii\web\User|null $creator
 *
 * @property SuperTicket $ticket
 * @property SuperUser $superUser
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
        return $this->hasOne(Yii::$app->user->identityClass, ['id' => 'created_by']);
    }

    public static function createTicketEvent($ticket_id, $type, $body, $super_user_id = null, $metadata = null)
    {
        if(empty($super_user_id)) {
            $superUser = SuperUser::findOne(['user_id' => Yii::$app->user->id]);

            $super_user_id = $superUser->id;
        }

        $event = new SuperTicketEvent();
        $event->ticket_id = $ticket_id;
        $event->super_user_id = $super_user_id;
        $event->type = $type;
        $event->body = $body;
        $event->metadata = $metadata;

        if ($event->save()) {
            SuperTicketFollower::follow($ticket_id, $super_user_id);

            if(in_array($type, [self::TYPE_COMMENT, self::TYPE_STATUS_CHANGE])) {
                $event->sendNotification();
            }

            return $event;
        }

        return null;
    }

    public function sendNotification()
    {
        $domainMailer = $this->ticket->domain->mailer;

        if($domainMailer && $domainMailer->host) {
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
        $mailer->htmlLayout = "/mail/layouts/html";

        foreach ($this->ticket->followers as $follower) {

            $mailer->compose("/mail/{$this->type}", [
                'event' => $this
            ])
                ->setFrom($domainMailer->from ?: 'no-reply@super.ticket')
                ->setTo($follower->superUser->email)
                ->setSubject($this->getNotificationSubject())
                ->send();
        }

        return true;
    }

    //TODO da finalizzare, scritto così è nammerda
    public function getNotificationSubject() {
        $subject = "[#T{$this->ticket_id}] - ";
        $subject .= Yii::t('super', "ticket_activity_{$this->type}", [
            'id' => $this->ticket_id,
            'subject' => $this->ticket->subject,
            'type' => $this->type,
        ]);

        return $subject;
    }
}

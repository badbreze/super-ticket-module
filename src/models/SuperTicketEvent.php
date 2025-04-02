<?php

namespace super\ticket\models;

use elitedivision\amos\attachments\behaviors\FileBehavior;
use elitedivision\amos\attachments\models\File;
use super\ticket\db\ActiveRecord;
use super\ticket\helpers\UserHelper;
use Yii;
use yii\base\Exception;
use super\ticket\mail\Mailer;
use yii\helpers\ArrayHelper;
use yii\web\Application;

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
    const TYPE_NOTE = 'note';
    const TYPE_ASSIGNEE = 'assignee';
    const TYPE_OPENING = 'opening';

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

    /**
     * @param $ticket_id
     * @param $type
     * @param $body
     * @param $super_user_id
     * @param $metadata
     * @return SuperTicketEvent
     * @throws \Exception
     */
    public static function createTicketEvent($ticket_id, $type, $body, $super_user_id = null, $metadata = null)
    {
        $event = new SuperTicketEvent();
        $event->ticket_id = $ticket_id;
        $event->type = $type;
        $event->body = $body;
        $event->metadata = json_encode($metadata);
        $event->super_user_id = $super_user_id;

        if ($event->save()) {
            foreach($event->getRecipients() as $recipient) {
                SuperTicketFollower::follow($ticket_id, $recipient->id);
            }

            //Event creator follows by default
            if($event->creator) {
                SuperTicketFollower::follow($ticket_id, $event->creator->id);
            }

            //TODO questa cosa della riapertura va riorganizzata in modo da avere codice più pulito
            if ($event->type == self::TYPE_COMMENT) {
                if ($event->ticket->status->identifier == SuperTicketStatus::STATUS_RESOLVED) {
                    //TODO va resettata la SLA come richiesto da marco
                    $event->ticket->changeStatus(SuperTicketStatus::STATUS_OPEN);
                }
            }

            if (in_array($type, [self::TYPE_COMMENT, self::TYPE_ASSIGNEE/*, self::TYPE_STATUS_CHANGE*/])) {
                $event->scheduleNotification();
            }

            return $event;
        } else {
            print_r($event->getErrors());
            throw new \Exception('Invalid Event Registration');
        }

        //VOID
    }

    public function scheduleNotification() {
        //TODO
        $notification = new SuperTicketEventNotification([
            'event_id' => $this->id,
            'status' => SuperTicketEventNotification::STATUS_PENDING
        ]);

        if(!$notification->save()) {
            throw new \Exception('Invalid Event Notification Registration');
        }

        return true;
    }

    public function  getRecipients()
    {
        //metadata used for special functions like custom recipients
        $metadata = !empty($this->metadata) ? json_decode($this->metadata, true) : [];
        $creatorId = $this->creator ? $this->creator->id : null;

        $exclusions = [$creatorId ?: $this->super_user_id, null];
        $fetchers = SuperMail::find()->select('address');

        if (!empty($metadata) && isset($metadata['recipients'])) {
            if(!is_array($metadata['recipients'])) {
                $recipients = array($metadata['recipients']);
            } else {
                $recipients = $metadata['recipients'];
            }

            $exclusions = array_diff($exclusions, $recipients);

            $rq = SuperUser::find()
                ->andWhere(['id' => $metadata['recipients']])
                ->andWhere(['not', ['id' => $exclusions]])
                ->andWhere(['not', ['email' => $fetchers]]);

            //print_r($rq->createCommand()->rawSql);

            if ($rq->count())
                return $rq->all();
        }

        $q = $this->ticket
            ->getFollowers($exclusions)
            ->andWhere(['not', ['email' => $fetchers]]);

        return $q->all();
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

<?php

namespace super\ticket\models;

use elitedivision\amos\attachments\behaviors\FileBehavior;
use elitedivision\amos\attachments\models\File;
use super\ticket\db\ActiveRecord;
use Yii;
use yii\base\Exception;
use super\ticket\mail\Mailer;
use yii\helpers\ArrayHelper;
use yii\web\Application;

/**
 * This is the model class for table "{{%super_ticket_event}}".
 *
 * @property int $id
 * @property string $status
 * @property int $event_id
 * @property string|null $created_at Creato il
 * @property string|null $updated_at Aggiornato il
 * @property string|null $deleted_at Cancellato il
 * @property int|null $created_by Creato da
 * @property int|null $updated_by Aggiornato da
 * @property int|null $deleted_by Cancellato da
 *
 * @property SuperTicketEvent $event
 */
class SuperTicketEventNotification extends ActiveRecord
{
    const STATUS_SENT = 'sent';
    const STATUS_PENDING = 'pending';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%super_ticket_event_notification}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'event_id'], 'required'],
            [['event_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['status'], 'string', 'max' => 255],
            [
                ['event_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => SuperTicketEvent::className(),
                'targetAttribute' => ['event_id' => 'id']
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('super', 'ID'),
            'status' => Yii::t('super', 'Status of Notification'),
            'event_id' => Yii::t('super', 'Event ID'),
            'created_at' => Yii::t('super', 'Created At'),
            'updated_at' => Yii::t('super', 'Updated At'),
            'deleted_at' => Yii::t('super', 'Deleted At'),
            'created_by' => Yii::t('super', 'Created By'),
            'updated_by' => Yii::t('super', 'Updated By'),
            'deleted_by' => Yii::t('super', 'Deleted By'),
        ];
    }

    /**
     * Gets query for [[TicketEvent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEvent()
    {
        return $this->hasOne(SuperTicketEvent::className(), ['id' => 'event_id']);
    }
}

<?php

namespace super\ticket\models;

use super\ticket\db\ActiveRecord;
use Yii;
use yii\db\Expression;

/**
 * @property int $id
 * @property string $name Name
 * @property int|null $schedule_id
 * @property int|null $sort
 * @property string|null $start_time
 * @property string|null $end_time
 * @property string|null $day_of_week
 * @property string|null $created_at Creato il
 * @property string|null $updated_at Aggiornato il
 * @property string|null $deleted_at Cancellato il
 * @property int|null $created_by Creato da
 * @property int|null $updated_by Aggiornato da
 * @property int|null $deleted_by Cancellato da
 */
class SuperTicketSlaScheduleEntries extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%super_ticket_sla_schedule_entries}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'schedule_id', 'start_time', 'end_time', 'day_of_week'], 'required'],
            [['schedule_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['name'], 'string', 'max' => 64],
            [['schedule_id'], 'exist', 'skipOnError' => true, 'targetClass' => SuperTicketSlaSchedule::className(), 'targetAttribute' => ['schedule_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('super', 'ID'),
            'name' => Yii::t('super', 'Name'),
            'schedule_id' => Yii::t('super', 'Schedule ID'),
            'sort' => Yii::t('super', 'Sorting'),
            'start_time' => Yii::t('super', 'Start Time'),
            'end_time' => Yii::t('super', 'End Time'),
            'day_of_week' => Yii::t('super', 'Day Of Week'),
            'created_at' => Yii::t('super', 'Created At'),
            'updated_at' => Yii::t('super', 'Updated At'),
            'deleted_at' => Yii::t('super', 'Deleted At'),
            'created_by' => Yii::t('super', 'Created By'),
            'updated_by' => Yii::t('super', 'Updated By'),
            'deleted_by' => Yii::t('super', 'Deleted By'),
        ];
    }

    /**
     * Gets query for [[SuperTicketSlaSchedule]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSchedule()
    {
        return $this->hasOne(SuperTicketSlaSchedule::className(), ['id' => 'schedule_id']);
    }

    /**
     * @return \DateTime
     * @throws \Exception
     */
    public function getEndHour()
    {
        return new \DateTime($this->end_time);
    }

    /**
     * @return \DateTime
     * @throws \Exception
     */
    public function getStartHour()
    {
        return new \DateTime($this->start_time);
    }

    public function getTotalHours()
    {
        return $this->getEndHour()->diff($this->getStartHour())->h;
    }

    public function getIsWorkHourByDateTime(\DateTime $dateTime)
    {
        $startHour = $this->getStartHour();
        $endHour = $this->getEndHour();

        return $dateTime >= $startHour && $dateTime <= $endHour;
    }
}

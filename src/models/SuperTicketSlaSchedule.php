<?php

namespace super\ticket\models;

use luya\admin\ngrest\plugins\Datetime;
use super\ticket\db\ActiveRecord;
use Yii;

/**
 * @property int $id
 * @property string $name Name
 * @property int|null $customer_id
 * @property string|null $created_at Creato il
 * @property string|null $updated_at Aggiornato il
 * @property string|null $deleted_at Cancellato il
 * @property int|null $created_by Creato da
 * @property int|null $updated_by Aggiornato da
 * @property int|null $deleted_by Cancellato da
 *
 * @property SuperTicketSlaScheduleEntries $entries
 */
class SuperTicketSlaSchedule extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%super_ticket_sla_schedule}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['customer_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['name'], 'string', 'max' => 64],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => SuperCustomer::className(), 'targetAttribute' => ['customer_id' => 'id']],
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
            'customer_id' => Yii::t('super', 'Customer ID'),
            'created_at' => Yii::t('super', 'Created At'),
            'updated_at' => Yii::t('super', 'Updated At'),
            'deleted_at' => Yii::t('super', 'Deleted At'),
            'created_by' => Yii::t('super', 'Created By'),
            'updated_by' => Yii::t('super', 'Updated By'),
            'deleted_by' => Yii::t('super', 'Deleted By'),
        ];
    }

    /**
     * Gets query for [[Customer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(SuperCustomer::className(), ['id' => 'customer_id']);
    }

    public function getEntries()
    {
        return $this->hasMany(SuperTicketSlaScheduleEntries::className(), ['schedule_id' => 'id']);
    }

    /**
     * @param \DateTime $date
     * @return \yii\db\ActiveQuery|SuperTicketSlaScheduleEntries
     */
    public function getEntryByDate(\DateTime $date)
    {
        $dow = $date->format('N');

        return $this->hasOne(SuperTicketSlaScheduleEntries::className(), ['schedule_id' => 'id'])
            ->where(['day_of_week' => $dow]);
    }

    public function getHolidaysByDateTime(\DateTime $date) {
        $day = $date->format('j');
        $month = $date->format('n');

        return SuperTicketSlaScheduleHolidays::find()
            ->where(['month' => $month, 'day' => $day])
            ->andWhere(['schedule_id' => $this->id]);
    }

    /**
     * @param \DateTime $date
     * @return \DateTime
     */
    public function getNextWorkingDayByDate(\DateTime $date) {
        for ($i = 1; $i < 7; $i++) { // Avoid loops on misconfigured schedules
            $entry = $this->getEntryByDate($date)->one();
            $holidays = $this->getHolidaysByDateTime($date)->one();

            if ($entry && !$holidays) {
                return $date;
            }

            $date->modify('+1 day');
            $date->setTime(0,0,0);
        }

        return null;
    }
}

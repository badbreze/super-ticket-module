<?php

namespace super\ticket\models;

use super\ticket\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "{{%super_ticket_priority}}".
 *
 * @property int $id
 * @property string $name Name
 * @property integer $grace_period Grace Period
 * @property int|null $customer_id
 * @property int $scheduling_id
 * @property string|null $created_at Creato il
 * @property string|null $updated_at Aggiornato il
 * @property string|null $deleted_at Cancellato il
 * @property int|null $created_by Creato da
 * @property int|null $updated_by Aggiornato da
 * @property int|null $deleted_by Cancellato da
 *
 * @property SuperCustomer $customer
 * @property SuperTicketSlaSchedule $scheduling
 */
class SuperTicketSla extends ActiveRecord
{
    public function __toString() {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%super_ticket_sla}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'grace_period', 'scheduling_id'], 'required'],
            [['grace_period', 'scheduling_id', 'customer_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
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
            'customer_id' => Yii::t('super', 'Customer'),
            'grace_period' => Yii::t('super', 'Grace Period'),
            'scheduling_id' => Yii::t('super', 'Scheduling'),
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

    /**
     * Gets query for [[SuperTicketSlaSchedule]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScheduling()
    {
        return $this->hasOne(SuperTicketSlaSchedule::className(), ['id' => 'scheduling_id']);
    }
}

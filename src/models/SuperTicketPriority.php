<?php

namespace super\ticket\models;

use super\ticket\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "{{%super_ticket_priority}}".
 *
 * @property int $id
 * @property string $name Name
 * @property string|null $identifier URL
 * @property int|null $customer_id
 * @property int|null $sla_id
 * @property int|null $weight
 * @property string|null $created_at Creato il
 * @property string|null $updated_at Aggiornato il
 * @property string|null $deleted_at Cancellato il
 * @property int|null $created_by Creato da
 * @property int|null $updated_by Aggiornato da
 * @property int|null $deleted_by Cancellato da
 *
 * @property SuperCustomer $customer
 * @property SuperTicketSla $sla
 * @property SuperMail[] $superMails
 * @property SuperTicket[] $superTickets
 */
class SuperTicketPriority extends ActiveRecord
{
    public function __toString() {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%super_ticket_priority}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['customer_id', 'sla_id', 'weight', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['name', 'identifier'], 'string', 'max' => 64],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => SuperCustomer::className(), 'targetAttribute' => ['customer_id' => 'id']],
            [['sla_id'], 'exist', 'skipOnError' => true, 'targetClass' => SuperTicketSla::className(), 'targetAttribute' => ['sla_id' => 'id']],
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
            'identifier' => Yii::t('super', 'Identifier'),
            'customer_id' => Yii::t('super', 'Customer ID'),
            'weight' => Yii::t('super', 'Weight'),
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
     * Gets query for [[Sla]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSla()
    {
        return $this->hasOne(SuperTicketSla::className(), ['id' => 'sla_id']);
    }

    /**
     * Gets query for [[SuperMail]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSuperMails()
    {
        return $this->hasMany(SuperMail::class, ['priority_id' => 'id']);
    }

    /**
     * Gets query for [[SuperTickets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSuperTickets()
    {
        return $this->hasMany(SuperTicket::className(), ['priority_id' => 'id']);
    }
}

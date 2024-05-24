<?php

namespace super\ticket\models;

use super\ticket\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "{{%super_customer_agent_mm}}".
 *
 * @property int $id
 * @property string|null $subject
 * @property string|null $content
 * @property int $type
 * @property int $ticket_id
 * @property int $related_ticket_id
 * @property string|null $created_at Created at
 * @property string|null $updated_at Updated at
 * @property string|null $deleted_at Deleted at
 * @property int|null $created_by Created by
 * @property int|null $updated_by Updated by
 * @property int|null $deleted_by Deleted by
 *
 * @property SuperTicket $ticket
 * @property SuperTicket $relatedTicket
 * @property User $createdBy
 * @property User $deletedBy
 * @property User $updatedBy
 */
class SuperTicketLink extends ActiveRecord
{
    const TYPE_COPY = 'copy';
    const TYPE_DEPENDS = 'depends';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%super_ticket_link}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ticket_id', 'related_ticket_id', 'type'], 'required'],
            [['ticket_id', 'related_ticket_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['content'], 'string'],
            [['subject'], 'string', 'max' => 128],
            [['ticket_id'], 'exist', 'skipOnError' => true, 'targetClass' => SuperTicket::className(), 'targetAttribute' => ['customer_id' => 'id']],
            [['related_ticket_id'], 'exist', 'skipOnError' => true, 'targetClass' => SuperTicket::className(), 'targetAttribute' => ['related_ticket_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['deleted_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['deleted_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('super', 'ID'),
            'ticket_id' => Yii::t('super', 'Ticket ID'),
            'related_ticket_id' => Yii::t('super', 'Related Ticket ID'),
            'type' => Yii::t('super', 'Type of Relation'),
            'created_at' => Yii::t('super', 'Created At'),
            'updated_at' => Yii::t('super', 'Updated At'),
            'deleted_at' => Yii::t('super', 'Deleted At'),
            'created_by' => Yii::t('super', 'Created By'),
            'updated_by' => Yii::t('super', 'Updated By'),
            'deleted_by' => Yii::t('super', 'Deleted By'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicket()
    {
        return $this->hasOne(SuperTicket::className(), ['id' => 'ticket_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelatedTicket()
    {
        return $this->hasOne(SuperTicket::className(), ['id' => 'related_ticket_id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Gets query for [[DeletedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeletedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'deleted_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }
}

<?php

namespace super\ticket\models;

use super\ticket\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "{{%super_customer_agent_mm}}".
 *
 * @property int $id
 * @property string|null $status
 * @property int $ticket_id
 * @property int $super_user_id
 * @property string|null $created_at Created at
 * @property string|null $updated_at Updated at
 * @property string|null $deleted_at Deleted at
 * @property int|null $created_by Created by
 * @property int|null $updated_by Updated by
 * @property int|null $deleted_by Deleted by
 *
 * @property SuperTicket $ticket
 * @property SuperUser $superUser
 * @property User $createdBy
 * @property User $deletedBy
 * @property User $updatedBy
 */
class SuperTicketFollower extends ActiveRecord
{
    const STATUS_FOLLOW = 'follow';
    const STATUS_UNFOLLOW = 'unfollow';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%super_ticket_follower}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ticket_id', 'super_user_id'], 'required'],
            [['ticket_id', 'super_user_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['status'], 'string', 'max' => 32],
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
            'ticket_id' => Yii::t('super', 'Ticket ID'),
            'super_user_id' => Yii::t('super', 'Super User ID'),
            'status' => Yii::t('super', 'Status'),
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
    public function getSuperUser()
    {
        return $this->hasOne(SuperUser::className(), ['id' => 'super_user_id']);
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

    /**
     * @param $ticket_id
     * @param $super_user_id
     * @return SuperTicketFollower
     */
    public static function follow($ticket_id, $super_user_id)
    {
        $follow = self::findOne([
                                    'ticket_id' => $ticket_id,
                                    'super_user_id' => $super_user_id
                                ]);

        if($follow) {
            return $follow;
        }

        $new = new SuperTicketFollower();
        $new->super_user_id = $super_user_id;
        $new->ticket_id = $ticket_id;
        $new->status = self::STATUS_FOLLOW;
        $new->save();

        return $new;
    }
}

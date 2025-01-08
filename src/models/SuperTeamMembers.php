<?php

namespace super\ticket\models;

use super\ticket\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "{{%super_customer_role_permission_mm}}".
 *
 * @property int $id
 * @property int $super_team_id
 * @property int $super_agent_id
 * @property string|null $created_at Created at
 * @property string|null $updated_at Updated at
 * @property string|null $deleted_at Deleted at
 * @property int|null $created_by Created by
 * @property int|null $updated_by Updated by
 * @property int|null $deleted_by Deleted by
 *
 * @property SuperTeam $team
 * @property SuperUser $agent
 */
class SuperTeamMembers extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%super_team_memebers}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['super_team_id', 'super_agent_id'], 'required'],
            [['super_team_id', 'super_agent_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['super_team_id'], 'exist', 'skipOnError' => true, 'targetClass' => SuperTeam::className(), 'targetAttribute' => ['super_team_id' => 'id']],
            [['super_agent_id'], 'exist', 'skipOnError' => true, 'targetClass' => SuperUser::className(), 'targetAttribute' => ['super_agent_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('super', 'ID'),
            'super_team_id' => Yii::t('super', 'Team ID'),
            'super_agent_id' => Yii::t('super', 'Agent ID'),
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
    public function getTeam()
    {
        return $this->hasOne(SuperTeam::className(), ['id' => 'super_team_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgent()
    {
        return $this->hasOne(SuperUser::className(), ['id' => 'super_agent_id']);
    }
}

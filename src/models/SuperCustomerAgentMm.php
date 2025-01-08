<?php

namespace super\ticket\models;

use super\ticket\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "{{%super_customer_agent_mm}}".
 *
 * @property int $id
 * @property int $agent_id
 * @property int $customer_id
 * @property int $customer_role_id
 * @property string|null $created_at Created at
 * @property string|null $updated_at Updated at
 * @property string|null $deleted_at Deleted at
 * @property int|null $created_by Created by
 * @property int|null $updated_by Updated by
 * @property int|null $deleted_by Deleted by
 *
 * @property SuperUser $agent
 * @property User $createdBy
 * @property SuperCustomer $customer
 * @property SuperCustomerRole $customerRole
 * @property User $deletedBy
 * @property User $updatedBy
 */
class SuperCustomerAgentMm extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%super_customer_agent_mm}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['agent_id', 'customer_id', 'customer_role_id'], 'required'],
            [['agent_id', 'customer_id', 'customer_role_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => SuperCustomer::className(), 'targetAttribute' => ['customer_id' => 'id']],
            [['agent_id'], 'exist', 'skipOnError' => true, 'targetClass' => SuperUser::className(), 'targetAttribute' => ['agent_id' => 'id']],
            [['customer_role_id'], 'exist', 'skipOnError' => true, 'targetClass' => SuperCustomerRole::className(), 'targetAttribute' => ['customer_role_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => Yii::$app->user->identityClass, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => Yii::$app->user->identityClass, 'targetAttribute' => ['updated_by' => 'id']],
            [['deleted_by'], 'exist', 'skipOnError' => true, 'targetClass' => Yii::$app->user->identityClass, 'targetAttribute' => ['deleted_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('super', 'ID'),
            'agent_id' => Yii::t('super', 'Agent ID'),
            'customer_id' => Yii::t('super', 'Customer ID'),
            'customer_role_id' => Yii::t('super', 'Customer Role ID'),
            'created_at' => Yii::t('super', 'Created At'),
            'updated_at' => Yii::t('super', 'Updated At'),
            'deleted_at' => Yii::t('super', 'Deleted At'),
            'created_by' => Yii::t('super', 'Created By'),
            'updated_by' => Yii::t('super', 'Updated By'),
            'deleted_by' => Yii::t('super', 'Deleted By'),
        ];
    }

    /**
     * Gets query for [[Agent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAgent()
    {
        return $this->hasOne(SuperUser::className(), ['id' => 'agent_id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(Yii::$app->user->identityClass, ['id' => 'created_by']);
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
     * Gets query for [[CustomerRole]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerRole()
    {
        return $this->hasOne(SuperCustomerRole::className(), ['id' => 'customer_role_id']);
    }

    /**
     * Gets query for [[DeletedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeletedBy()
    {
        return $this->hasOne(Yii::$app->user->identityClass, ['id' => 'deleted_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(Yii::$app->user->identityClass, ['id' => 'updated_by']);
    }
}

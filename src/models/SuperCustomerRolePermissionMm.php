<?php

namespace super\ticket\models;

use super\ticket\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "{{%super_customer_role_permission_mm}}".
 *
 * @property int $id
 * @property int $customer_role_id
 * @property int $customer_role_permission_id
 * @property string|null $created_at Created at
 * @property string|null $updated_at Updated at
 * @property string|null $deleted_at Deleted at
 * @property int|null $created_by Created by
 * @property int|null $updated_by Updated by
 * @property int|null $deleted_by Deleted by
 *
 * @property SuperCustomerRole $customerRole
 * @property SuperCustomerRolePermission $customerRolePermission
 */
class SuperCustomerRolePermissionMm extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%super_customer_role_permission_mm}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_role_id', 'customer_role_permission_id'], 'required'],
            [['customer_role_id', 'customer_role_permission_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['customer_role_id'], 'exist', 'skipOnError' => true, 'targetClass' => SuperCustomerRole::className(), 'targetAttribute' => ['customer_role_id' => 'id']],
            [['customer_role_permission_id'], 'exist', 'skipOnError' => true, 'targetClass' => SuperCustomerRolePermission::className(), 'targetAttribute' => ['customer_role_permission_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('super', 'ID'),
            'customer_role_id' => Yii::t('super', 'Customer Role ID'),
            'customer_role_permission_id' => Yii::t('super', 'Customer Role Permission ID'),
            'created_at' => Yii::t('super', 'Created At'),
            'updated_at' => Yii::t('super', 'Updated At'),
            'deleted_at' => Yii::t('super', 'Deleted At'),
            'created_by' => Yii::t('super', 'Created By'),
            'updated_by' => Yii::t('super', 'Updated By'),
            'deleted_by' => Yii::t('super', 'Deleted By'),
        ];
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
     * Gets query for [[CustomerRolePermission]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerRolePermission()
    {
        return $this->hasOne(SuperCustomerRolePermission::className(), ['id' => 'customer_role_permission_id']);
    }
}

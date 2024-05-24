<?php

namespace super\ticket\models;

use super\ticket\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "{{%super_customer_role_permission}}".
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $identifier
 *
 * @property SuperCustomerRolePermissionMm[] $superCustomerRolePermissionMms
 */
class SuperCustomerRolePermission extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%super_customer_role_permission}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'identifier'], 'required'],
            [['name', 'description', 'identifier'], 'string', 'max' => 255],
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
            'description' => Yii::t('super', 'Description'),
            'identifier' => Yii::t('super', 'Identifier'),
        ];
    }

    /**
     * Gets query for [[SuperCustomerRolePermissionMms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSuperCustomerRolePermissionMms()
    {
        return $this->hasMany(SuperCustomerRolePermissionMm::className(), ['customer_role_permission_id' => 'id']);
    }
}

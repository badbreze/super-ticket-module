<?php

namespace super\ticket\models;

use super\ticket\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "{{%super_customer_role}}".
 *
 * @property int $id
 * @property string $name Name
 * @property string|null $description URL
 * @property int|null $customer_id
 * @property string|null $created_at Creato il
 * @property string|null $updated_at Aggiornato il
 * @property string|null $deleted_at Cancellato il
 * @property int|null $created_by Creato da
 * @property int|null $updated_by Aggiornato da
 * @property int|null $deleted_by Cancellato da
 *
 * @property User $createdBy
 * @property SuperCustomer $customer
 * @property User $deletedBy
 * @property SuperCustomerAgentMm[] $superCustomerAgentMms
 * @property SuperCustomerRolePermissionMm[] $superCustomerRolePermissionMms
 * @property User $updatedBy
 */
class SuperCustomerRole extends ActiveRecord
{
    public const ROLE_OWNER = 1;
    public const ROLE_ADMIN = 2;
    public const ROLE_MANAGER = 3;
    public const ROLE_VIEWER = 4;
    public const ROLE_AGENT = 5;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%super_customer_role}}';
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
            [['description'], 'string', 'max' => 256],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => SuperCustomer::className(), 'targetAttribute' => ['customer_id' => 'id']],
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
            'name' => Yii::t('super', 'Name'),
            'description' => Yii::t('super', 'Description'),
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
     * Gets query for [[DeletedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeletedBy()
    {
        return $this->hasOne(Yii::$app->user->identityClass, ['id' => 'deleted_by']);
    }

    /**
     * Gets query for [[SuperCustomerAgentMms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSuperCustomerAgentMms()
    {
        return $this->hasMany(SuperCustomerAgentMm::className(), ['customer_role_id' => 'id']);
    }

    /**
     * Gets query for [[SuperCustomerRolePermissionMms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSuperCustomerRolePermissionMms()
    {
        return $this->hasMany(SuperCustomerRolePermissionMm::className(), ['customer_role_id' => 'id']);
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

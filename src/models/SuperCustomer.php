<?php

namespace super\ticket\models;

use app\models\User;
use super\ticket\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "{{%super_customer}}".
 *
 * @property int $id
 * @property string $name Name
 * @property string|null $description URL
 * @property string|null $created_at Creato il
 * @property string|null $updated_at Aggiornato il
 * @property string|null $deleted_at Cancellato il
 * @property int|null $created_by Creato da
 * @property int|null $updated_by Aggiornato da
 * @property int|null $deleted_by Cancellato da
 *
 * @property User $createdBy
 * @property User $deletedBy
 * @property SuperCustomerAgentMm[] $superCustomerAgentMms
 * @property SuperCustomerRole[] $superCustomerRoles
 * @property SuperDomain[] $superDomains
 * @property SuperTicketPriority[] $superTicketPriorities
 * @property SuperTicketStatus[] $superTicketStatuses
 * @property User $updatedBy
 */
class SuperCustomer extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%super_customer}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['name'], 'string', 'max' => 64],
            [['description'], 'string', 'max' => 256],
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
     * Gets query for [[SuperCustomerAgentMms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSuperCustomerAgentMms()
    {
        return $this->hasMany(SuperCustomerAgentMm::className(), ['customer_id' => 'id']);
    }

    /**
     * Gets query for [[SuperCustomerRoles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSuperCustomerRoles()
    {
        return $this->hasMany(SuperCustomerRole::className(), ['customer_id' => 'id']);
    }

    /**
     * Gets query for [[SuperDomains]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSuperDomains()
    {
        return $this->hasMany(SuperDomain::className(), ['customer_id' => 'id']);
    }

    /**
     * Gets query for [[SuperTicketPriorities]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSuperTicketPriorities()
    {
        return $this->hasMany(SuperTicketPriority::className(), ['customer_id' => 'id']);
    }

    /**
     * Gets query for [[SuperTicketStatuses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSuperTicketStatuses()
    {
        return $this->hasMany(SuperTicketStatus::className(), ['customer_id' => 'id']);
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

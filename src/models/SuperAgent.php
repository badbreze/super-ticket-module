<?php

namespace super\ticket\models;

use super\ticket\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "{{%super_agent}}".
 *
 * @property int $id
 * @property string $name Name
 * @property string|null $surname
 * @property string|null $url URL
 * @property string|null $address
 * @property string|null $email
 * @property int|null $super_user_id
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
 * @property SuperTeam[] $superTeams
 * @property SuperMail[] $superMails
 * @property SuperTicket[] $superTickets
 * @property User $updatedBy
 * @property SuperUser $superUser
 * @property string $fullName
 */
class SuperAgent extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%super_agent}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['super_user_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['name', 'surname'], 'string', 'max' => 64],
            [['url', 'email'], 'string', 'max' => 128],
            [['address'], 'string', 'max' => 255],
            [['super_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => SuperUser::className(), 'targetAttribute' => ['super_user_id' => 'id']],
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
            'surname' => Yii::t('super', 'Surname'),
            'url' => Yii::t('super', 'Url'),
            'address' => Yii::t('super', 'Address'),
            'email' => Yii::t('super', 'Email'),
            'super_user_id' => Yii::t('super', 'Super User ID'),
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
        return $this->hasMany(SuperCustomerAgentMm::className(), ['agent_id' => 'id']);
    }

    /**
     * Gets query for [[SuperTeams]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSuperTeams()
    {
        return $this->hasMany(SuperTeam::className(), ['agent_id' => 'id']);
    }

    /**
     * Gets query for [[SuperMail]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSuperMails()
    {
        return $this->hasMany(SuperMail::class, ['agent_id' => 'id']);
    }

    /**
     * Gets query for [[SuperTickets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSuperTickets()
    {
        return $this->hasMany(SuperTicket::class, ['agent_id' => 'id']);
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

    /**
     * Gets query for [[SuperUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSuperUser()
    {
        return $this->hasOne(SuperUser::className(), ['id' => 'super_user_id']);
    }

    public function getFullName() {
        return $this->name . ' ' . $this->surname;
    }
}

<?php

namespace super\ticket\models;

use super\ticket\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "{{%super_domain}}".
 *
 * @property int $id
 * @property string $name Name
 * @property string|null $description URL
 * @property int $customer_id
 * @property string|null $created_at Creato il
 * @property string|null $updated_at Aggiornato il
 * @property string|null $deleted_at Cancellato il
 * @property int|null $created_by Creato da
 * @property int|null $updated_by Aggiornato da
 * @property int|null $deleted_by Cancellato da
 *
 * @property SuperCustomer $customer
 * @property SuperTeam[] $superTeams
 * @property SuperMail[] $superMails
 * @property SuperUser[] $superUsers
 * @property SuperUser[] $superUsers0
 * @property SuperMailer $mailer
 */
class SuperDomain extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%super_domain}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'customer_id'], 'required'],
            [['customer_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['name'], 'string', 'max' => 64],
            [['description'], 'string', 'max' => 256],
            [
                ['customer_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => SuperCustomer::className(),
                'targetAttribute' => ['customer_id' => 'id']
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
     * Gets query for [[Customer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(SuperCustomer::className(), ['id' => 'customer_id']);
    }

    /**
     * Gets query for [[SuperTeams]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSuperTeams()
    {
        return $this->hasMany(SuperTeam::className(), ['domain_id' => 'id']);
    }

    /**
     * Gets query for [[SuperMail]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSuperMails()
    {
        return $this->hasMany(SuperMail::class, ['domain_id' => 'id']);
    }

    /**
     * Gets query for [[SuperUsers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSuperUsers()
    {
        return $this->hasMany(SuperUser::className(), ['domain_id' => 'id']);
    }

    /**
     * Gets query for [[SuperUsers0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSuperUsers0()
    {
        return $this->hasMany(SuperUser::className(), ['domain_id' => 'id']);
    }

    public function getNewTickets()
    {
        return $this->hasMany(SuperTicket::className(), ['domain_id' => 'id'])
            ->andWhere(['status_id' => 1]);
    }

    public function getResolvedTickets()
    {
        return $this->hasMany(SuperTicket::className(), ['domain_id' => 'id'])
            ->andWhere(['status_id' => 2]);
    }

    public function getMailer() {
        return $this->hasOne(SuperMailer::className(), ['domain_id' => 'id']);
    }
}

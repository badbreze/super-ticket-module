<?php

namespace super\ticket\models;

use super\ticket\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "{{%super_user}}".
 *
 * @property int $id
 * @property string $name Name
 * @property string|null $surname
 * @property string|null $email
 * @property string|null $pnone
 * @property int $domain_id
 * @property int $user_id
 * @property string|null $created_at Creato il
 * @property string|null $updated_at Aggiornato il
 * @property string|null $deleted_at Cancellato il
 * @property int|null $created_by Creato da
 * @property int|null $updated_by Aggiornato da
 * @property int|null $deleted_by Cancellato da
 * @property string $fullName
 *
 * @property SuperDomain $domain
 * @property SuperTicket[] $superTickets
 * @property yii\web\User $user
 */
class SuperUser extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%super_user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['domain_id', 'user_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['name', 'surname', 'phone'], 'string', 'max' => 64],
            [['email'], 'string', 'max' => 128],
            [['user_id'], 'unique'],
            [['domain_id'], 'exist', 'skipOnError' => true, 'targetClass' => SuperDomain::className(), 'targetAttribute' => ['domain_id' => 'id']],
            //[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
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
            'domain_id' => Yii::t('super', 'Domain ID'),
            'user_id' => Yii::t('super', 'User ID'),
            'created_at' => Yii::t('super', 'Created At'),
            'updated_at' => Yii::t('super', 'Updated At'),
            'deleted_at' => Yii::t('super', 'Deleted At'),
            'created_by' => Yii::t('super', 'Created By'),
            'updated_by' => Yii::t('super', 'Updated By'),
            'deleted_by' => Yii::t('super', 'Deleted By'),
        ];
    }

    /**
     * Gets query for [[Domain]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDomain()
    {
        return $this->hasOne(SuperDomain::className(), ['id' => 'domain_id']);
    }

    /**
     * Gets query for [[SuperTickets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSuperTickets()
    {
        return $this->hasMany(SuperTicket::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Yii::$app->user->identityClass, ['id' => 'user_id']);
    }

    /**
     * @return string
     */
    public function getFullName() {
        return $this->name .' '. $this->surname;
    }
}

<?php

namespace super\ticket\models;

use super\ticket\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "{{%super_mail}}".
 *
 * @property int $id
 * @property string $name Name
 * @property string|null $username
 * @property string|null $password
 * @property string|null $host
 * @property int|null $port
 * @property string|null $encryption
 * @property int $skip_ssl_validation
 * @property string|null $from
 * @property string|null $metadata
 * @property int|null $domain_id
 * @property string|null $mail_template
 *
 * @property string|null $created_at Creato il
 * @property string|null $updated_at Aggiornato il
 * @property string|null $deleted_at Cancellato il
 * @property int|null $created_by Creato da
 * @property int|null $updated_by Aggiornato da
 * @property int|null $deleted_by Cancellato da
 *
 * @property SuperDomain $domain
 * @property string $folder
 */
class SuperMailer extends ActiveRecord
{
    public const ENCRYPTION_TLS = 'tls';
    public const ENCRYPTION_SSL = 'ssl';
    public const ENCRYPTION_NONE = null;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%super_mailer}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['domain_id'], 'required'],
            [['domain_id', 'port', 'skip_ssl_validation', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['username', 'password', 'host', 'encryption', 'from', 'metadata'], 'string', 'max' => 255],
            [['mail_template'], 'string'],
            [['mail_template'], 'default', 'value'=> '{{content}}'],
            [['domain_id'], 'exist', 'skipOnError' => true, 'targetClass' => SuperDomain::className(), 'targetAttribute' => ['domain_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('super', 'ID'),
            'username' => Yii::t('super', 'Username'),
            'password' => Yii::t('super', 'Password'),
            'host' => Yii::t('super', 'Hostname'),
            'port' => Yii::t('super', 'Service Port'),
            'encryption' => Yii::t('super', 'Mail Transport Encryption'),
            'skip_ssl_validation' => Yii::t('super', 'Skip SSL Validation'),
            'from' => Yii::t('super', 'From Address'),
            'data' => Yii::t('super', 'Data'),
            'domain_id' => Yii::t('super', 'Domain ID'),
            'created_at' => Yii::t('super', 'Created At'),
            'updated_at' => Yii::t('super', 'Updated At'),
            'deleted_at' => Yii::t('super', 'Deleted At'),
            'created_by' => Yii::t('super', 'Created By'),
            'updated_by' => Yii::t('super', 'Updated By'),
            'deleted_by' => Yii::t('super', 'Deleted By'),
        ];
    }

    /**
     * Gets query for [[Domain0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDomain()
    {
        return $this->hasOne(SuperDomain::className(), ['id' => 'domain_id']);
    }

}

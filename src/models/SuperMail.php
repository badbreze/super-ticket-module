<?php

namespace super\ticket\models;

use super\ticket\db\ActiveRecord;
use super\ticket\modules\console\base\MailBox;
use super\ticket\modules\console\helpers\EmailHelper;
use Yii;

/**
 * This is the model class for table "{{%super_mail}}".
 *
 * @property int $id
 * @property int $enabled Enabled
 * @property string $name Name
 * @property string|null $username
 * @property string|null $password
 * @property string|null $host
 * @property int|null $port
 * @property string|null $type
 * @property int $skip_ssl_validation
 * @property string|null $address
 * @property string|null $metadata
 * @property int|null $domain_id
 * @property int $team_id
 * @property int $status_id
 * @property int|null $priority_id
 * @property int|null $agent_id
 * @property string|null $path
 * @property string|null $move_path
 *
 * @property string|null $created_at Creato il
 * @property string|null $updated_at Aggiornato il
 * @property string|null $deleted_at Cancellato il
 * @property int|null $created_by Creato da
 * @property int|null $updated_by Aggiornato da
 * @property int|null $deleted_by Cancellato da
 *
 * @property SuperDomain $domain
 * @property SuperUser $agent
 * @property SuperTeam $team
 * @property SuperTicketPriority $priority
 * @property SuperTicketStatus $status
 * @property string $folder
 */
class SuperMail extends ActiveRecord
{
    public const TYPE_IMAP = 'imap';
    public const TYPE_IMAP_SSL = 'imap_ssl';
    public const TYPE_POP = 'pop';
    public const TYPE_POP_SSL = 'pop_ssl';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%super_mail}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'team_id', 'domain_id'], 'required'],
            [
                [
                    'enabled',
                    'domain_id',
                    'team_id',
                    'status_id',
                    'priority_id',
                    'agent_id',
                    'port',
                    'skip_ssl_validation',
                    'created_by',
                    'updated_by',
                    'deleted_by'
                ],
                'integer'
            ],
            [['enabled'], 'checkConfig'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['name', 'path', 'move_path'], 'string', 'max' => 64],
            [['username', 'password', 'host', 'type', 'address', 'metadata'], 'string', 'max' => 255],
            [
                ['domain_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => SuperDomain::className(),
                'targetAttribute' => ['domain_id' => 'id']
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
            'enabled' => Yii::t('super', 'Enabled'),
            'name' => Yii::t('super', 'Name'),
            'username' => Yii::t('super', 'Username'),
            'password' => Yii::t('super', 'Password'),
            'host' => Yii::t('super', 'Hostname'),
            'port' => Yii::t('super', 'Service Port'),
            'type' => Yii::t('super', 'Mail Type'),
            'skip_ssl_validation' => Yii::t('super', 'Skip SSL Validation'),
            'address' => Yii::t('super', 'Mail Address'),
            'data' => Yii::t('super', 'Data'),
            'domain_id' => Yii::t('super', 'Domain ID'),
            'path' => Yii::t('super', 'Fetch Path'),
            'move_path' => Yii::t('super', 'Move Path'),
            'created_at' => Yii::t('super', 'Created At'),
            'updated_at' => Yii::t('super', 'Updated At'),
            'deleted_at' => Yii::t('super', 'Deleted At'),
            'created_by' => Yii::t('super', 'Created By'),
            'updated_by' => Yii::t('super', 'Updated By'),
            'deleted_by' => Yii::t('super', 'Deleted By'),
        ];
    }

    public function checkConfig($model)
    {
        if (!$this->enabled) {
            return true;
        }

        $mailbox = new MailBox([
                                   'connection' => EmailHelper::getMailBoxConnection($this)
                               ]);

        try {
            $mailbox->getMailIds();
            return true;
        } catch (\Exception $e) {
            $this->addError(
                'enabled',
                \Yii::t(
                    'super', 'Mail Configuration is Invalid ({error})',
                    ['error' => $e->getMessage()]
                )
            );
        }

        return false;
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
     * Gets query for [[Domain0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDomain()
    {
        return $this->hasOne(SuperDomain::className(), ['id' => 'domain_id']);
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
     * Gets query for [[Team]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeam()
    {
        return $this->hasOne(SuperTeam::className(), ['id' => 'team_id']);
    }

    /**
     * Gets query for [[Priority]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPriority()
    {
        return $this->hasOne(SuperTicketPriority::className(), ['id' => 'priority_id']);
    }

    /**
     * Gets query for [[Status]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(SuperTicketStatus::className(), ['id' => 'status_id']);
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

    public function getFolder()
    {
        return empty($this->path) ? 'INBOX' : $this->path;
    }
}

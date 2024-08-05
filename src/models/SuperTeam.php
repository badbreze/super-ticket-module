<?php

namespace super\ticket\models;

use super\ticket\db\ActiveRecord;
use super\ticket\helpers\StringHelper;
use Yii;

/**
 * This is the model class for table "{{%super_team}}".
 *
 * @property int $id
 * @property string $name Name
 * @property string|null $description
 * @property int $domain_id
 * @property int|null $agent_id
 * @property string|null $mail_signature
 * @property string|null $created_at Creato il
 * @property string|null $updated_at Aggiornato il
 * @property string|null $deleted_at Cancellato il
 * @property int|null $created_by Creato da
 * @property int|null $updated_by Aggiornato da
 * @property int|null $deleted_by Cancellato da
 *
 * @property SuperAgent $agent
 * @property SuperDomain $domain
 * @property SuperMail[] $superMails
 * @property SuperTicket[] $superTickets
 * @property string $signature
 */
class SuperTeam extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%super_team}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['domain_id', 'agent_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['name'], 'string', 'max' => 64],
            [['mail_signature'], 'string'],
            [['description'], 'string', 'max' => 255],
            [['domain_id'], 'exist', 'skipOnError' => true, 'targetClass' => SuperDomain::className(), 'targetAttribute' => ['domain_id' => 'id']],
            [['agent_id'], 'exist', 'skipOnError' => true, 'targetClass' => SuperAgent::className(), 'targetAttribute' => ['agent_id' => 'id']],
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
            'domain_id' => Yii::t('super', 'Domain'),
            'agent_id' => Yii::t('super', 'Leader Agent'),
            'mail_signature' => Yii::t('super', 'Team Signature'),
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
        return $this->hasOne(SuperAgent::className(), ['id' => 'agent_id']);
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
        return $this->hasMany(SuperTicket::className(), ['team_id' => 'id']);
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

    public function getSignature() {

        return StringHelper::parse($this->mail_signature, ['team' => $this]);
    }
}

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
 * @property SuperUser $agent
 * @property SuperDomain $domain
 * @property SuperMail[] $superMails
 * @property SuperTicket[] $superTickets
 * @property string $signature
 * @property SuperUser[] $availableMembers
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
            [['agent_id'], 'exist', 'skipOnError' => true, 'targetClass' => SuperUser::className(), 'targetAttribute' => ['agent_id' => 'id']],
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

    public function __toString()
    {
        return $this->name;
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

    public function getTeamMembersMM()
    {
        return $this->hasMany(SuperTeamMembers::class, ['super_team_id' => 'id']);
    }

    public function getTeamMembers()
    {
        return $this->hasMany(SuperUser::class, ['id' => 'super_agent_id'])->via('teamMembersMM');
    }

    public function getSignature() {

        return StringHelper::parse($this->mail_signature, ['team' => $this]);
    }

    public function getAvailableMembers()
    {
        return SuperUser::find()
            ->joinWith('customers.domains')
            ->andWhere([
                'OR',
                [
                    'AND',
                    ['super_domain.id' => $this->domain_id],
                    ['customer_role_id' => [
                        SuperCustomerRole::ROLE_OWNER,
                        SuperCustomerRole::ROLE_ADMIN,
                        SuperCustomerRole::ROLE_AGENT,
                        SuperCustomerRole::ROLE_MANAGER,
                    ]
                    ]
                ],
                ['super_user.domain_id' => null],
            ])
            ->all();
    }
}

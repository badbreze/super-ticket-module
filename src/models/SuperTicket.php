<?php

namespace super\ticket\models;

use elitedivision\amos\attachments\behaviors\FileBehavior;
use elitedivision\amos\attachments\models\File;
use super\ticket\db\ActiveRecord;
use Yii;
use yii\base\Event;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%super_ticket}}".
 *
 * @property int $id
 * @property string $subject Name
 * @property string $content
 * @property int $status_id
 * @property int|null $priority_id
 * @property int $user_id
 * @property string|null $source_type
 * @property int|null $source_id
 * @property string|null $source_enum
 * @property int|null $team_id
 * @property int|null $domain_id
 * @property int|null $agent_id
 * @property string|null $due_date
 * @property string|null $metadata
 * @property string|null $created_at Creato il
 * @property string|null $updated_at Aggiornato il
 * @property string|null $deleted_at Cancellato il
 * @property int|null $created_by Creato da
 * @property int|null $updated_by Aggiornato da
 * @property int|null $deleted_by Cancellato da
 *
 * @property SuperAgent $agent
 * @property SuperTeam $team
 * @property SuperDomain $domain
 * @property SuperTicketPriority $priority
 * @property SuperMail $mail
 * @property SuperTicketStatus $status
 * @property SuperUser $superUser
 * @property SuperTicketEvent[] $events
 * @property SuperTicketEvent[] $comments
 * @property SuperTicketEvent $lastEvent
 * @property SuperTicket[] $relatedTickets
 * @property SuperTicket[] $dependantTickets
 * @property SuperTicketLink[] $links
 * @property SuperTicketLink[] $reverseLinks
 * @property SuperTicketStatus[] $availableStatuses
 * @property SuperTicketPriority[] $availablePriorities
 * @property SuperAgent[] $availableAssignees
 * @property SuperTicketFollower[] $followers
 * @property File[] $attachments
 */
class SuperTicket extends ActiveRecord
{
    const SOURCE_MAIL = 'mail';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%super_ticket}}';
    }

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'fileBehavior' => [
                'class' => FileBehavior::className()
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subject', 'content', 'status_id', 'super_user_id', 'source_id', 'team_id', 'domain_id'], 'required'],
            [['source_type', 'source_enum', 'content', 'metadata'], 'string'],
            [
                [
                    'status_id',
                    'priority_id',
                    'super_user_id',
                    'source_id',
                    'team_id',
                    'domain_id',
                    'agent_id',
                    'created_by',
                    'updated_by',
                    'deleted_by'
                ],
                'integer'
            ],
            [['due_date', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['subject'], 'string', 'max' => 255],

            [['attachments'], 'file', 'maxFiles' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('super', 'ID'),
            'subject' => Yii::t('super', 'Subject'),
            'content' => Yii::t('super', 'Content'),
            'status_id' => Yii::t('super', 'Status ID'),
            'priority_id' => Yii::t('super', 'Priority ID'),
            'super_user_id' => Yii::t('super', 'User ID'),
            'source_type' => Yii::t('super', 'Source Type'),
            'source_id' => Yii::t('super', 'Source ID'),
            'source_enum' => Yii::t('super', 'Source ENUM'),
            'team_id' => Yii::t('super', 'Team ID'),
            'agent_id' => Yii::t('super', 'Agent ID'),
            'due_date' => Yii::t('super', 'Due Date'),
            'created_at' => Yii::t('super', 'Created At'),
            'updated_at' => Yii::t('super', 'Updated At'),
            'deleted_at' => Yii::t('super', 'Deleted At'),
            'created_by' => Yii::t('super', 'Created By'),
            'updated_by' => Yii::t('super', 'Updated By'),
            'deleted_by' => Yii::t('super', 'Deleted By'),
        ];
    }

    public function beforeSave($insert)
    {
        if (!$this->isNewRecord) {
            //TODO eseguire solo in casistiche particolari?
            $this->refreshDueDate();
        }

        return parent::beforeSave($insert);
    }

    public function refreshDueDate()
    {
        $startDate = new \DateTime($this->created_at);


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
     * Gets query for [[Team]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeam()
    {
        return $this->hasOne(SuperTeam::className(), ['id' => 'team_id']);
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
     * Gets query for [[Priority]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPriority()
    {
        return $this->hasOne(SuperTicketPriority::class, ['id' => 'priority_id']);
    }

    /**
     * Gets query for [[Source]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSource()
    {
        //TODO gestire tipo sorgente
        return $this->hasOne(SuperTicketSource::class, ['id' => 'source_id']);
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
     * Gets query for [[SuperUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSuperUser()
    {
        return $this->hasOne(SuperUser::className(), ['id' => 'super_user_id']);
    }

    /**
     * Gets query for [[SuperTicketEvents]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEvents()
    {
        return $this->hasMany(SuperTicketEvent::className(), ['ticket_id' => 'id']);
    }

    /**
     * Gets query for [[SuperTicketEvents]] typed ad Comments.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(SuperTicketEvent::className(), ['ticket_id' => 'id'])
            ->andOnCondition(['super_ticket_event.type' => SuperTicketEvent::TYPE_COMMENT]);
    }

    public function getLastEvent() {
        return self::getEvents()->orderBy(['created_at' => SORT_DESC])->one();
    }

    /**
     * Gets query for [[SuperTicketEvents]].
     *
     * @param $exclusions integer user to exclude
     * @return \yii\db\ActiveQuery
     */
    public function getFollowers($exclusions = 0)
    {
        return $this
            ->hasMany(SuperTicketFollower::className(), ['ticket_id' => 'id'])
            ->andOnCondition(['<>', 'super_user_id', $exclusions])
            ->andWhere(['status' => SuperTicketFollower::STATUS_FOLLOW]);
    }

    public function addEvent($type, $body, $super_user_id = null, $metadata = null)
    {
        return SuperTicketEvent::createTicketEvent($this->id, $type, $body, $super_user_id, $metadata);
    }

    /**
     * Gets query for [[SuperTicketLink]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLinks()
    {
        return $this->hasMany(SuperTicketLink::className(), ['ticket_id' => 'id']);
    }

    /**
     * Gets query for [[SuperTicketLink]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReverseLinks()
    {
        return $this->hasMany(SuperTicketLink::className(), ['related_ticket_id' => 'id']);
    }

    /**
     * Gets query for [[SuperTicketLink]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRelatedTickets()
    {
        return $this->hasMany(SuperTicket::className(), ['id' => 'related_ticket_id'])->via('links');
    }

    /**
     * Gets query for [[SuperTicketLink]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDependantTickets()
    {
        return $this->hasMany(SuperTicket::className(), ['id' => 'ticket_id'])
            ->via('reverseLinks', function (ActiveQuery $relation) {
                return $relation->andWhere(['type' => SuperTicketLink::TYPE_DEPENDS]);
            });
    }

    //TODO gestire aorrettamente gli stati disponibili
    public function getAvailableStatuses()
    {
        return SuperTicketStatus::find()->all();
    }

    public function getAvailablePriorities()
    {
        return SuperTicketPriority::find()->all();
    }

    public function getAvailableAssignees()
    {
        return SuperAgent::find()->all();
    }

    public function updateAssignee($agent_id)
    {
        $this->agent_id = $agent_id;

        $this->addEvent(
            SuperTicketEvent::TYPE_ASSIGNEE,
            Yii::t('super', 'assigned to {user}', ['user' => $this->agent->fullName]),
            $this->agent->super_user_id
        );

        return $this->save(false);
    }

    public function updatePriority($priority_id)
    {
        $this->priority_id = $priority_id;

        $this->addEvent(
            SuperTicketEvent::TYPE_PRIORITY,
            Yii::t('super', 'Changed the priority to {priority}', ['priority' => $this->priority->name])
        );

        return $this->save(false);
    }

    public function updateStatus($status_id)
    {
        $this->status_id = $status_id;

        $this->addEvent(
            SuperTicketEvent::TYPE_STATUS_CHANGE,
            Yii::t('super', 'Marked as {status}', ['status' => $this->status->name])
        );

        return $this->save(false);
    }

    public function changeStatus($identifier) {
        $status = SuperTicketStatus::findOne(['identifier' => $identifier]);

        if($status && $status->id) {
            $this->status_id = $status->id;
            return $this->save(false);
        }

        throw new \Exception("The Status {$identifier} doe not exists");
    }
}

<?php

namespace super\ticket\models;

use elitedivision\amos\attachments\behaviors\FileBehavior;
use elitedivision\amos\attachments\models\File;
use super\ticket\db\ActiveRecord;
use super\ticket\helpers\DateTimeHelper;
use super\ticket\helpers\UserHelper;
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
 * @property SuperUser $agent
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
 * @property SuperUser[] $availableAssignees
 * @property SuperTicketFollower[] $followers
 * @property SuperUser[] $followable
 * @property File[] $attachments
 * @property bool $isDueDateElapsed
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

    public function getLastEvent()
    {
        return self::getEvents()->orderBy(['created_at' => SORT_DESC])->one();
    }

    /**
     * Gets query for [[SuperTicketEvents]].
     *
     * @param $exclusions integer|array users to exclude
     * @return \yii\db\ActiveQuery
     */
    public function getFollowers($exclusions = 0)
    {
        return $this
            ->hasMany(SuperTicketFollower::className(), ['ticket_id' => 'id'])
            ->andOnCondition(['not', ['super_user_id' => $exclusions]])
            ->andWhere(['status' => SuperTicketFollower::STATUS_FOLLOW]);
    }

    /**
     * @param $exclusions
     * @return ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getFollowable($exclusions = 0) {
        //Basic Filter
        $q = SuperUser::find()
            ->andWhere(['not', ['id' => $exclusions]]);

        //Magic Getter to result rows
        $q->multiple = true;

        if(Yii::$app->user->can('SUPER_ADMIN')) {
            return $q;
        }

        $q->andWhere([
            'OR',
            ['id' => $this->getFollowers()->select('super_user_id')],
            ['domain_id' => $this->domain_id]
        ]);

//print_r($q->createCommand()->rawSql);die;
        return $q;
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
        return SuperTicketPriority::find()->orderBy(['weight' => SORT_ASC])->all();
    }

    public function getAvailableAssignees()
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

    public function updateAssignee($agent_id)
    {
        $this->agent_id = $agent_id;

        $this->addEvent(
            SuperTicketEvent::TYPE_ASSIGNEE,
            Yii::t('super', 'assigned to {user}', ['user' => $this->agent->fullName]),
            $this->agent->id
        );

        return $this->save(false);
    }

    /**
     * @param $priority_id
     * @return bool
     */
    public function updatePriority($priority_id)
    {
        $this->priority_id = $priority_id;

        $this->due_date = $this->calculateDueDate();

        $this->addEvent(
            SuperTicketEvent::TYPE_PRIORITY,
            Yii::t('super', 'Changed the priority to {priority}', ['priority' => $this->priority->name])
        );

        return $this->save(false);
    }

    /**
     * Calculates the due date based on SLA, working hours and holidays
     * @return string|null Returns formatted due date or null if no SLA is set
     */
    public function calculateDueDate()
    {
        // Return null if no SLA or priority is set
        if (!$this->priority_id) {
            return null;
        }

        //try {
        // Get current datetime as starting point
        $startDate = new \DateTime();

        // If ticket has created_at, use it as start date
        if ($this->created_at) {
            $startDate = new \DateTime($this->created_at);

            //TODO logica da vincolare a un'opzione, magari non tutti la vogliono così
            if($startDate->format('i') > 0) {
                $startDate->modify('+1 hour');
                $startDate->setTime($startDate->format('H'),0,0);
            }
        }

        if ($this->priority->sla === null) {
            return null;
        }

        $dueDate = $this->addWorkingHours($startDate);

        // Format the date for database
        return $dueDate->format('Y-m-d H:i:s');

        /*} catch (\Exception $e) {
            Yii::error('Error calculating due date: ' . $e->getMessage());
            return null;
        }*/
    }

    /**
     * Add working hours to a date while considering holidays and working hours
     * @param \DateTime $startDate
     * @param int $hoursToAdd
     * @return \DateTime
     */
    private function addWorkingHours(\DateTime $startDate)
    {
        $dueDate = clone $startDate;
        $remainingHours = $this->priority->sla->grace_period;

        while ($remainingHours > 0) {
            $dueDate = $this->priority->sla->schedule->getNextWorkingDayByDate($dueDate);

            if(!$dueDate) {
                return null;
            }

            $workDay = $this->priority->sla->schedule->getEntryByDate($dueDate)->one();
            $diffDueToWork = DateTimeHelper::compareTimeOnly($dueDate, $workDay->getEndHour());

            if($diffDueToWork == '+' ) {
                $dueDate->modify('+1 day');
                continue;
            }

            if($workDay->getStartHour()->format('H') > $dueDate->format('H')) {
                $dueDate->setTime(
                    $workDay->getStartHour()->format('H'),
                    $workDay->getStartHour()->format('i')
                );
            }

            // Calculate hours until end of current working day
            $hoursUntilEnd = (int)$workDay->getEndHour()->format('H') - (int)$dueDate->format('H');

            if ($remainingHours <= $hoursUntilEnd) {
                // Add remaining hours
                $dueDate->modify("+{$remainingHours} hours");
                $remainingHours = 0;
            } else {
                // Add hours until end of day and continue with next day
                $dueDate->modify("+{$hoursUntilEnd} hours");
                $remainingHours -= $hoursUntilEnd;
                $dueDate->modify('+1 day');
                $dueDate->setTime(9, 0); // Reset to start of working day
            }
        }

        return $dueDate;
    }

    /**
     * @param $status_id
     * @return bool
     */
    public function updateStatus($status_id)
    {
        $this->status_id = $status_id;

        $this->addEvent(
            SuperTicketEvent::TYPE_STATUS_CHANGE,
            Yii::t('super', 'Marked as {status}', ['status' =>  Yii::t('super', $this->status->name)])
        );

        return $this->save(false);
    }

    public function changeStatus($identifier)
    {
        $status = SuperTicketStatus::findOne(['identifier' => $identifier]);

        if ($status && $status->id) {
            $this->status_id = $status->id;
            return $this->save(false);
        }

        throw new \Exception("The Status {$identifier} doe not exists");
    }

    public function getIsDueDateElapsed()
    {
        if ($this->due_date) {
            $dueDate = new \DateTime($this->due_date);
            $now = new \DateTime();

            return $dueDate < $now;
        }

        return false;
    }
}

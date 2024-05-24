<?php
namespace super\ticket\behaviors;

use super\ticket\models\SuperTicket;
use Yii;
use yii\base\Behavior;
use super\ticket\db\ActiveRecord;
use yii\base\Event;
use yii\base\ModelEvent;
use yii\helpers\ArrayHelper;

/**
 * Soft Delete Behavior
 *
 * @property ActiveRecord $owner
 */
class EventNotifyBehavior extends Behavior
{
    /**
     * @inheritdoc
     */
    public function events()
    {
        return ArrayHelper::merge(parent::events(), [
            SuperTicket::EVENT_TICKET_EVENT => 'processEventCreation'
        ]);
    }

    public function processEventCreation(Event $event)
    {
        //
    }
}
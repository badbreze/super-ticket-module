<?php
namespace super\ticket\behaviors;

use Yii;
use yii\base\Behavior;
use super\ticket\db\ActiveRecord;
use yii\base\ModelEvent;
use yii\helpers\ArrayHelper;

/**
 * Soft Delete Behavior
 *
 * @property ActiveRecord $owner
 */
class SoftDeleteBehavior extends Behavior
{
    public $deletedAtAttribute = 'deleted_at';
    public $deletedByAttribute = 'deleted_by';

    /**
     * @inheritdoc
     */
    public function events()
    {
        return ArrayHelper::merge(parent::events(), [
            ActiveRecord::EVENT_BEFORE_DELETE => 'overlapDeteleEvent'
        ]);
    }

    /**
     * Set the attribute with the current timestamp to mark as deleted
     *
     * @param ModelEvent $event
     */
    public function overlapDeteleEvent($event)
    {
        if(!$this->owner->hasSoftDelete()) {
            return $event;
        }

        $userId = !\Yii::$app->user->isGuest ? \Yii::$app->user->id : null;

        $this->owner->{$this->deletedByAttribute} = $userId;
        $this->owner->{$this->deletedAtAttribute} = date('Y-m-d H:i:s');

        // save record
        $this->owner->save(false, [$this->deletedAtAttribute, $this->deletedByAttribute]);

        return $event->isValid = false;
    }

    /**
     * Restore soft-deleted record
     */
    public function restore()
    {
        // mark attribute as null
        $attribute = $this->attribute;
        $this->owner->$attribute = null;

        // save record
        $this->owner->save(false, [$attribute]);
    }

    /**
     * Delete record from database regardless of the $safeMode attribute
     */
    public function forceDelete()
    {
        // store model so that we can detach the behavior and delete as normal
        $model = $this->owner;
        $this->detach();
        $model->delete();
    }
}
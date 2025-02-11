<?php
namespace super\ticket\models\forms;

use super\ticket\helpers\UserHelper;
use super\ticket\models\SuperTicket;
use super\ticket\models\SuperTicketStatus;
use Yii;
use yii\base\Model;

/**
 * Login form
 */
class SuperTicketBulkForm extends Model
{
    public array $selection;
    public $status;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['selection'], 'required'],
            [['status'], 'string'],
            [['selection'], 'canUpdate'],
        ];
    }

    //TODO gestire aorrettamente gli stati disponibili
    public function getAvailableStatuses()
    {
        return SuperTicketStatus::find()->all();
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'status' => Yii::t('super', 'Status'),
            'selection' => Yii::t('super', 'Selection'),
        ];
    }

    public function canUpdate() {
        foreach ($this->selection as $item) {
            $ticket = SuperTicket::findOne($item);

            //TODO gestire permesso
            if(false && !Yii::$app->user->can('updateTicket', ['ticket' => $ticket])) {
                $this->addError('selection', Yii::t('super', 'You are not allowed to edit this ticket.'));
                return false;
            }
        }

        return true;
    }

    public function updateTickets() {
        //TODO gestire meglio errori
        foreach ($this->selection as $item) {
            $ticket = SuperTicket::findOne($item);

            if(!empty($this->status) && !$ticket->changeStatus($this->status)) {
                $this->addError('selection', Yii::t('super', 'There was an error while updating the ticket.'));
                return false;
            }
        }

        return true;
    }

    public function canDelete() {
        foreach ($this->selection as $item) {
            $ticket = SuperTicket::findOne($item);

            //TODO gestire permesso
            if(false && !Yii::$app->user->can('deleteTicket', ['ticket' => $ticket])) {
                $this->addError('selection', Yii::t('super', 'You are not allowed to delete this ticket.'));
                return false;
            }
        }

        return true;
    }

    public function deleteTickets() {
        //TODO gestire meglio errori
        foreach ($this->selection as $item) {
            $ticket = SuperTicket::findOne($item);

            if(!empty($this->status) && !$ticket->delete()) {
                $this->addError('selection', Yii::t('super', 'There was an error while deleting the ticket.'));
                return false;
            }
        }

        return true;
    }

}
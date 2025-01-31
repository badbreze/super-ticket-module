<?php

namespace super\ticket\modules\console\commands;

use super\ticket\models\SuperTicket;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Email console controller for the `super` module
 */
class TicketController extends Controller
{
    public function actionIndex()
    {
        //Debug
        print_r("Doing Things\n");
    }

    public function actionFixDueDate()
    {
        $ticketsWithoutDate = SuperTicket::find()
            ->andWhere(['due_date' => null])
            ->andWhere(['not', ['priority_id' => null]]);

        foreach ($ticketsWithoutDate->all() as $ticket) {
            $ticket->updatePriority($ticket->priority_id);
        }
    }

}

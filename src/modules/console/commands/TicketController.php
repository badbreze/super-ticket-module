<?php

namespace super\ticket\modules\console\commands;

use elitedivision\amos\attachments\FileModule;
use super\ticket\helpers\AttachmentsHelper;
use super\ticket\helpers\UserHelper;
use super\ticket\mail\MailSubject;
use super\ticket\models\SuperMail;
use super\ticket\models\SuperTicket;
use super\ticket\models\SuperTicketEvent;
use super\ticket\models\SuperTicketFollower;
use super\ticket\models\SuperTicketLink;
use super\ticket\modules\console\base\MailBox;
use super\ticket\modules\console\helpers\EmailHelper;
use yii\base\Exception;
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
        Console::stdout("Doing Things\n");
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

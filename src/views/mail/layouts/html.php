<?php

use super\ticket\models\SuperTicketEvent;
use super\ticket\helpers\StringHelper;

/**
 * @var $this \yii\web\View
 * @var $content string
 * @var $event SuperTicketEvent
 */

$template = $event->ticket->domain->mailer->mail_template;

if ($event->type == SuperTicketEvent::TYPE_COMMENT) {
    //REPLY Placeholder
    echo "-----\n";
    echo "<br/>";

    echo StringHelper::parse($template, ['content' => $content, 'event' => $event]);
} else {
    echo $this->render("../{$event->type}", ['content' => $content, 'event' => $event]);
}
?>
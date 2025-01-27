<?php

use super\ticket\models\SuperTicketEvent;
use super\ticket\helpers\StringHelper;

/**
 * @var $this \yii\web\View
 * @var $content string
 * @var $event SuperTicketEvent
 */

$template = $event->ticket->domain->mailer->mail_template;

//REPLY Placeholder
echo "-----\n";

if($event->type == SuperTicketEvent::TYPE_COMMENT)
    echo StringHelper::parse($template,['content' => $content, 'event' => $event]);
else {
    echo $this->render("../{$event->type}", ['content' => $content, 'event' => $event]);
}
?>
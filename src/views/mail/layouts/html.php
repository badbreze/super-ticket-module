<?php

use super\ticket\models\SuperTicketEvent;
use super\ticket\helpers\StringHelper;

/**
 * @var $this \yii\web\View
 * @var $content string
 * @var $event SuperTicketEvent
 */

$template = $event->ticket->domain->mailer->mail_template;

echo StringHelper::parse($template,['content' => $content, 'event' => $event]);
?>
<!--
<p>
    Ricevi questa email perchè sei un follower del ticket <a href="<?= \super\ticket\helpers\RouteHelper::toTicket($event->ticket_id, null, true); ?>"><?= $event->ticket->subject; ?></a>
</p>
-->
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
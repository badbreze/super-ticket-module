<?php
use super\ticket\helpers\HtmlHelper;

/**
 * @var $this \yii\web\View
 * @var $event \super\ticket\models\SuperTicketEvent
 */
?>
<div class="ticket-generic-event-single">
    <h6>Evento generico di <?= $event->creator; ?></h6>
    <b><?= HtmlHelper::fullClean($event->body); ?></b>
    -
    <i>type: <?= $event->type; ?></i>
</div>
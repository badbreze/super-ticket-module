<?php
use super\ticket\helpers\HtmlHelper;

/**
 * @var $this \yii\web\View
 * @var $event \super\ticket\models\SuperTicketEvent
 */
?>
<div class="ticket-status-single">
    <h6><?= $event->created_by; ?> ha cambiato lo stato del ticket in</h6>
    <b><?= HtmlHelper::fullClean($event->body); ?></b>
    -
    <i>type: <?= $event->type; ?></i>
</div>
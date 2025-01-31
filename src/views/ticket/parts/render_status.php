<?php
use super\ticket\helpers\HtmlHelper;

/**
 * @var $this \yii\web\View
 * @var $event \super\ticket\models\SuperTicketEvent
 */
?>
<div class="ticket-status-single mt-4 mb-4">
    <h6><i class="fas fa-caret-right"></i> <strong><?= $event->creator; ?></strong></h6>
    <b class="ml-4"><i class="fas fa-stream"></i> <?= HtmlHelper::fullClean($event->body); ?></b>
</div>
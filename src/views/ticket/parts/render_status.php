<?php
use super\ticket\helpers\HtmlHelper;

/**
 * @var $this \yii\web\View
 * @var $event \super\ticket\models\SuperTicketEvent
 */
?>
<div class="ticket-status-single mt-4 mb-4">
    <h6>Cambio stato da parte di <strong><?= $event->creator; ?></strong></h6>
    <b><?= HtmlHelper::fullClean($event->body); ?></b>
</div>
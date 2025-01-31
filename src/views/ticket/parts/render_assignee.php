<?php
use super\ticket\helpers\HtmlHelper;

/**
 * @var $this \yii\web\View
 * @var $event \super\ticket\models\SuperTicketEvent
 */
?>
<div class="ticket-generic-event-single">
    <?php if($event->creator): ?>
        <h6>
            <i class="fas fa-caret-right"></i> <strong><?= $event->creator; ?></strong>
        </h6>
    <?php endif; ?>
    <b class="ml-4">
        <i class="fas fa-user-edit"></i> <?= HtmlHelper::fullClean($event->body); ?>
    </b>

    <!--div class="recipients">
        Destinatari:
        <?php
        foreach ($event->getRecipients() as $rec) {
            echo $rec->fullName;
        }
        ?>
    </div-->
</div>
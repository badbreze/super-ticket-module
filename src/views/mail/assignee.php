<?php

use super\ticket\models\SuperTicketEvent;

/**
 * @var $this \yii\web\View
 * @var $event SuperTicketEvent
 * @var $content string|null
 */
?>
<?= Yii::t('super', 'Ticket assigned to {user}', ['user' => $event->ticket->agent->fullName]); ?>

<?php if ($event->creator) : ?>
    <?= Yii::t('super', 'by {user}', ['user' => $event->creator->fullName]); ?>
<?php endif; ?>

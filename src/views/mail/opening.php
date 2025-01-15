<?php
use super\ticket\models\SuperTicketEvent;
/**
 * @var $this \yii\web\View
 * @var $event SuperTicketEvent
 */
?>

<?= $event->creator; ?> ha aperto il ticket "<?= $event->ticket->subject; ?>"

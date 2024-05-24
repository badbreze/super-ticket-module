<?php
use super\ticket\models\SuperTicketEvent;
/**
 * @var $this \yii\web\View
 * @var $event SuperTicketEvent
 */
?>

<?= $event->creator; ?> ha cambiato assegnatario al ticket "<?= $event->ticket->subject; ?>"

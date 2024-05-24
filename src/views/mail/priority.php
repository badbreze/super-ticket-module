<?php
use super\ticket\models\SuperTicketEvent;
/**
 * @var $this \yii\web\View
 * @var $event SuperTicketEvent
 */
?>

<?= $event->creator; ?> ha cambiato la priorità del ticket "<?=$event->ticket->subject; ?>"

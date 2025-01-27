<?php
use super\ticket\models\SuperTicketEvent;
/**
 * @var $this \yii\web\View
 * @var $event SuperTicketEvent
 * @var $content string|null
 */
?>

<?= $event->creator; ?> ha cambiato la priorità del ticket "<?=$event->ticket->subject; ?>"

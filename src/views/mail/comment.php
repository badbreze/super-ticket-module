<?php
use super\ticket\models\SuperTicketEvent;
/**
 * @var $this \yii\web\View
 * @var $event SuperTicketEvent
 * @var $content string|null
 */
?>

<?= $event->superUser->fullName; ?> ha commentato il ticket "<?=$event->ticket->subject; ?>"

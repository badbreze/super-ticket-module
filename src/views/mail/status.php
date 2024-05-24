<?php
use super\ticket\models\SuperTicketEvent;
/**
 * @var $this \yii\web\View
 * @var $event SuperTicketEvent
 */
?>

Nuovo stato <?= $event->type; ?> da <?= $event->creator; ?>

<?php
use super\ticket\models\SuperTicketEvent;
/**
 * @var $this \yii\web\View
 * @var $event SuperTicketEvent
 * @var $content string|null
 */
?>

Nuovo stato <?= $event->type; ?> da <?= $event->creator; ?>

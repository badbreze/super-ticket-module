<?php
use super\ticket\helpers\TicketHelper;
use super\ticket\helpers\DomainHelper;
use super\ticket\helpers\StatusHelper;
use super\ticket\models\SuperTicket;
use yii\web\View;

/**
 * @var $this View
 * @var $model \super\ticket\models\SuperUser
 */
?>
<!--div class="p-2 bd-highlight">
    <img class="ticket-avatar rounded-circle"
         src="https://www.gravatar.com/avatar/<?= md5(
             $model->email
         ); ?>?d=mp"
         alt="avatar"
    />
</div-->

<div class="p-2 bd-highlight">
    <b><?= $model->name . ' ' . $model->surname; ?></b>
    <div><i><?= $model->email; ?></i></div>
</div>
<?php

use yii\helpers\Html;
use super\ticket\helpers\RouteHelper;
use yii\web\View;

/**
 * @var $this View
 * @var $model \super\ticket\models\SuperTicket
 */
?>
<div class="p-2 bd-highlight">
    <b>
        <?= Html::a($model->subject, RouteHelper::toTicket($model->id)); ?>
        <?php if($model->attachments) : ?>
            <i class="fas fa-paperclip"></i>
        <?php endif; ?>
    </b>
    <p>
        <i>
            <?= Yii::t('super', 'By'); ?>
            <?= $model->superUser->fullName; ?>
        </i>
    </p>
</div>
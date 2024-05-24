<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var super\ticket\models\SuperTicketSla $model
*/

$this->title = Yii::t('models', 'Super Ticket Sla');
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Super Ticket Sla'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud super-ticket-sla-create">

    <h1>
                <?= Html::encode($model->name) ?>
        <small>
            <?= Yii::t('models', 'Super Ticket Sla') ?>
        </small>
    </h1>

    <div class="clearfix crud-navigation">
        <div class="pull-left">
            <?=             Html::a(
            Yii::t('app', 'Cancel'),
            \yii\helpers\Url::previous(),
            ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <hr />

    <?= $this->render('_form', [
    'model' => $model,
    ]); ?>

</div>

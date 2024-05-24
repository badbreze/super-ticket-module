<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var super\ticket\models\SuperTicketSla $model
*/

$this->title = Yii::t('models', 'Super Ticket Sla');
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Super Ticket Sla'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edit');
?>
<div class="giiant-crud super-ticket-sla-update">

    <h1>
                <?= Html::encode($model->name) ?>

        <small>
            <?= Yii::t('models', 'Super Ticket Sla') ?>        </small>
    </h1>

    <?php echo $this->render('_form', [
    'model' => $model,
    ]); ?>

</div>

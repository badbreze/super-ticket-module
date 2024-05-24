<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var super\ticket\models\SuperDomain $model
* @var super\ticket\models\SuperMailer $mailer
*/

$this->title = Yii::t('models', 'Super Domain');
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Super Domain'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud super-domain-create">

    <h1>
                <?= Html::encode($model->name) ?>
        <small>
            <?= Yii::t('models', 'Super Domain') ?>
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
    'mailer' => $mailer,
    ]); ?>

</div>

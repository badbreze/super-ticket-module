<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var super\ticket\models\SuperDomain $model
 * @var super\ticket\models\SuperMailer $mailer
 */

$this->title = Yii::t('models', 'Super Domain');
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Super Domain'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edit');
?>
<div class="giiant-crud super-domain-update">

    <h1>
        <?= Html::encode($model->name) ?>

        <small>
            <?= Yii::t('models', 'Super Domain') ?>        </small>
    </h1>

    <?php echo $this->render('_form', [
        'model' => $model,
        'mailer' => $mailer,
    ]); ?>

</div>

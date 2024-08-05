<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var super\ticket\models\SuperMail $model
 * @var super\ticket\models\SuperMailer $mailer
 */

$this->title = Yii::t('models', 'Super Mail');
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Super Mail'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edit');
?>
<div class="giiant-crud super-mail-update">

    <h1>
        <?= Html::encode($model->name) ?>

        <small>
            <?= Yii::t('models', 'Super Mail') ?>        </small>
    </h1>

    <?php echo $this->render('_form', [
        'model' => $model,
        'mailer' => $mailer,
    ]); ?>

</div>

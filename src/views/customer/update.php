<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var super\ticket\models\SuperCustomer $model
 */

$this->title = Yii::t('models', 'Super Customer');
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Super Customer'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edit');
?>
<div class="giiant-crud super-customer-update">

    <h1>
        <?= Html::encode($model->name) ?>

        <small>
            <?= Yii::t('models', 'Super Customer') ?>
        </small>
    </h1>

    <?php echo $this->render('_form', [
        'model' => $model,
    ]); ?>

</div>

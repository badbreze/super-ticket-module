<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var super\ticket\models\SuperTeam $model
 * @var \yii\data\ActiveDataProvider $membersDataProvider
 */

$this->title = Yii::t('models', 'Super Team');
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Super Team'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edit');
?>
<div class="giiant-crud super-team-update">

    <h1>
        <?= Html::encode($model->name) ?>

        <small>
            <?= Yii::t('models', 'Super Team') ?>        </small>
    </h1>

    <?php echo $this->render('_form', [
        'model' => $model,
        'membersDataProvider' => $membersDataProvider
    ]); ?>

</div>

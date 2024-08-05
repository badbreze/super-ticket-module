<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Tabs;
use dosamigos\tinymce\TinyMce;
use super\ticket\models\SuperMailer;

/**
* @var yii\web\View $this
* @var super\ticket\models\SuperUser $model
*/

$this->title = Yii::t('models', 'Super User');
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Super User'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud super-user-init-create">

    <h1>
                <?= Html::encode($model->name) ?>
        <small>
            <?= Yii::t('models', 'Super User') ?>
        </small>
    </h1>

    <hr />

    <p><?= Yii::t('super', 'Build Your Super Ticket System Profile'); ?></p>


    <div class="super-user-init-form">

        <?php $form = ActiveForm::begin([
                                            'id' => 'SuperUser',
                                            'layout' => 'horizontal',
                                            'enableClientValidation' => true,
                                            'errorSummaryCssClass' => 'error-summary alert alert-danger',
                                            'fieldConfig' => [
                                                'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                                                'horizontalCssClasses' => [
                                                    'label' => 'col-sm-2',
                                                    #'offset' => 'col-sm-offset-4',
                                                    'wrapper' => 'col-sm-8',
                                                    'error' => '',
                                                    'hint' => '',
                                                ],
                                            ],
                                        ]
        );
        ?>

        <div class="section">
            <?php $this->beginBlock('main'); ?>

            <p>
                <!-- attribute name -->
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                <!-- attribute surname -->
                <?= $form->field($model, 'surname')->textInput(['maxlength' => true]) ?>

                <!-- attribute email -->
                <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

                <!-- attribute phone -->
                <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

            </p>
            <?php $this->endBlock(); ?>

            <?=
            Tabs::widget(
                [
                    'encodeLabels' => false,
                    'items' => [
                        [
                            'label' => Yii::t('models', 'SuperUser'),
                            'content' => $this->blocks['main'],
                            'active' => true,
                        ],
                    ]
                ]
            );
            ?>
            <hr/>

            <?php echo $form->errorSummary($model); ?>

            <?= Html::submitButton(
                '<span class="glyphicon glyphicon-check"></span> ' .
                ($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save')),
                [
                    'id' => 'save-' . $model->formName(),
                    'class' => 'btn btn-success'
                ]
            );
            ?>

            <?php ActiveForm::end(); ?>

        </div>

    </div>


</div>

<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Tabs;
use dosamigos\tinymce\TinyMce;
use yii\grid\GridView;

/**
 * @var yii\web\View $this
 * @var super\ticket\models\SuperTeam $model
 * @var yii\widgets\ActiveForm $form
 * @var \yii\data\ActiveDataProvider $membersDataProvider
 */
?>

<div class="super-team-form">

    <?php $form = ActiveForm::begin([
                                        'id' => 'SuperTeam',
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

    <div class="">
        <?php $this->beginBlock('main'); ?>

        <p>


            <!-- attribute name -->
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <!-- attribute domain_id -->
            <?= // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::activeField
            $form->field($model, 'domain_id')->dropDownList(
                \yii\helpers\ArrayHelper::map(super\ticket\models\SuperDomain::find()->all(), 'id', 'name'),
                [
                    'prompt' => Yii::t('app', 'Select'),
                    'disabled' => (isset($relAttributes) && isset($relAttributes['domain_id'])),
                ]
            ); ?>

            <!-- attribute agent_id -->
            <?= // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::activeField
            $form->field($model, 'agent_id')->dropDownList(
                \yii\helpers\ArrayHelper::map($model->availableMembers, 'id', 'fullName'),
                [
                    'prompt' => Yii::t('app', 'Select'),
                    'disabled' => (isset($relAttributes) && isset($relAttributes['agent_id'])),
                ]
            ); ?>

            <!-- attribute description -->
            <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

            <!-- attribute mail_signature -->
            <?= $form->field($model, 'mail_signature')->widget(TinyMce::className(), [
                //'name' => 'test',
                'options' => ['rows' => 10],
                //'language' => 'en_GB',
                'clientOptions' => [
                    'menubar' => false,
                    'statusbar' => false,
                    'toolbar' => 'undo redo | formatselect | bold italic',
                ]
            ]); ?>

        </p>
        <?php $this->endBlock(); ?>

        <?php $this->beginBlock('members'); ?>

        <div class="table-responsive">
            <?= GridView::widget([
                'dataProvider' => $membersDataProvider,
                'columns' => [
                    'name',
                    'surname',
                    'email',
                    'phone',
                    'domain',
                ]
            ]); ?>
        </div>

        <?php $this->endBlock(); ?>

        <?=
        Tabs::widget(
            [
                'encodeLabels' => false,
                'items' => [
                    [
                        'label' => Yii::t('super', 'Team'),
                        'content' => $this->blocks['main'],
                        'active' => true,
                    ],
                    [
                        'label' => Yii::t('super', 'Members'),
                        'content' => $this->blocks['members'],
                        'active' => false,
                    ],
                ]
            ]
        );
        ?>
        <hr/>

        <?php echo $form->errorSummary($model); ?>

        <?= Html::submitButton(
            '<span class="glyphicon glyphicon-check"></span> ' .
            ($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('super', 'Save')),
            [
                'id' => 'save-' . $model->formName(),
                'class' => 'btn btn-success'
            ]
        );
        ?>

        <?php ActiveForm::end(); ?>

    </div>

</div>


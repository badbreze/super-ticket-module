<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Tabs;
use super\ticket\models\SuperMail;

/**
 * @var yii\web\View $this
 * @var super\ticket\models\SuperMail $model
 * @var yii\widgets\ActiveForm $form
 */

?>

<div class="super-mail-form">

    <?php $form = ActiveForm::begin([
                                        'id' => 'SuperMail',
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
            <!-- attribute enabled -->
            <?= $form->field($model, 'enabled')->checkbox() ?>

            <!-- attribute name -->
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>


            <!-- attribute name -->
            <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>


            <!-- attribute name -->
            <?= $form->field($model, 'password')->textInput(['maxlength' => true]) ?>


            <!-- attribute name -->
            <?= $form->field($model, 'host')->textInput(['maxlength' => true]) ?>


            <!-- attribute name -->
            <?= $form->field($model, 'port')->textInput(['maxlength' => true]) ?>

            <!-- attribute name -->
            <?= // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::activeField
            $form->field($model, 'type')->dropDownList(
                [
                        SuperMail::TYPE_IMAP => 'Imap',
                        SuperMail::TYPE_IMAP_SSL => 'Imap SSL',
                        SuperMail::TYPE_POP => 'Pop',
                        SuperMail::TYPE_POP_SSL => 'Pop SSL',
                ],
                [
                    'prompt' => Yii::t('app', 'Select'),
                    'disabled' => (isset($relAttributes) && isset($relAttributes['domain_id'])),
                ]
            ); ?>


            <!-- attribute skip_ssl_validation -->
            <?= $form->field($model, 'skip_ssl_validation')->checkbox() ?>

            <!-- attribute address -->
            <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

            <!-- attribute path -->
            <?= $form->field($model, 'path')->textInput(['maxlength' => true]) ?>

            <!-- attribute move_path -->
            <?= $form->field($model, 'move_path')->textInput(['maxlength' => true]) ?>

            <!-- attribute domain_id -->
            <?= // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::activeField
            $form->field($model, 'domain_id')->dropDownList(
                \yii\helpers\ArrayHelper::map(super\ticket\models\SuperDomain::find()->all(), 'id', 'name'),
                [
                    'prompt' => Yii::t('app', 'Select'),
                    'disabled' => (isset($relAttributes) && isset($relAttributes['domain_id'])),
                ]
            ); ?>

            <!-- attribute team_id -->
            <?= // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::activeField
            $form->field($model, 'team_id')->dropDownList(
                \yii\helpers\ArrayHelper::map(super\ticket\models\SuperTeam::find()->all(), 'id', 'name'),
                [
                    'prompt' => Yii::t('app', 'Select'),
                    'disabled' => (isset($relAttributes) && isset($relAttributes['team_id'])),
                ]
            ); ?>

            <!-- attribute status_id -->
            <?= // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::activeField
            $form->field($model, 'status_id')->dropDownList(
                \yii\helpers\ArrayHelper::map(super\ticket\models\SuperTicketStatus::find()->all(), 'id', 'name'),
                [
                    'prompt' => Yii::t('app', 'Select'),
                    'disabled' => (isset($relAttributes) && isset($relAttributes['status_id'])),
                ]
            ); ?>

            <!-- attribute priority_id -->
            <?= // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::activeField
            $form->field($model, 'priority_id')->dropDownList(
                \yii\helpers\ArrayHelper::map(super\ticket\models\SuperTicketPriority::find()->all(), 'id', 'name'),
                [
                    'prompt' => Yii::t('app', 'Select'),
                    'disabled' => (isset($relAttributes) && isset($relAttributes['priority_id'])),
                ]
            ); ?>

            <!-- attribute agent_id -->
            <?= // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::activeField
            $form->field($model, 'agent_id')->dropDownList(
                \yii\helpers\ArrayHelper::map(super\ticket\models\SuperAgent::find()->all(), 'id', 'name'),
                [
                    'prompt' => Yii::t('app', 'Select'),
                    'disabled' => (isset($relAttributes) && isset($relAttributes['agent_id'])),
                ]
            ); ?>


        </p>
        <?php $this->endBlock(); ?>

        <?=
        Tabs::widget(
            [
                'encodeLabels' => false,
                'items' => [
                    [
                        'label' => Yii::t('models', 'SuperMail'),
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


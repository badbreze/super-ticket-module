<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Tabs;
use dosamigos\tinymce\TinyMce;

/**
 * @var yii\web\View $this
 * @var super\ticket\models\SuperDomain $model
 * @var super\ticket\models\SuperMailer $mailer
 * @var yii\widgets\ActiveForm $form
 */

?>

<div class="super-domain-form">

    <?php $form = ActiveForm::begin([
                                        'id' => 'SuperDomain',
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

            <!-- attribute customer_id -->
            <?= // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::activeField
            $form->field($model, 'customer_id')->dropDownList(
                \yii\helpers\ArrayHelper::map(super\ticket\models\SuperCustomer::find()->all(), 'id', 'name'),
                [
                    'prompt' => Yii::t('app', 'Select'),
                    'disabled' => (isset($relAttributes) && isset($relAttributes['customer_id'])),
                ]
            ); ?>

            <!-- attribute description -->
            <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

            <!-- attribute mail_template -->
            <?= $form->field($mailer, 'mail_template')->widget(TinyMce::className(), [
                //'name' => 'test',
                'options' => ['rows' => 10],
                //'language' => 'en_GB',
                'clientOptions' => [
                    'menubar' => false,
                    'statusbar' => false,
                    //'toolbar' => 'undo redo | formatselect | bold italic',
                ]
            ])->label(false); ?>

        </p>
        <?php $this->endBlock(); ?>

        <?php $this->beginBlock('mailer'); ?>

        <p>
            <!-- attribute username -->
            <?= $form->field($mailer, 'username')->textInput(['maxlength' => true]) ?>

            <!-- attribute password -->
            <?= $form->field($mailer, 'password')->textInput(['maxlength' => true]) ?>

            <!-- attribute host -->
            <?= $form->field($mailer, 'host')->textInput(['maxlength' => true]) ?>

            <!-- attribute port -->
            <?= $form->field($mailer, 'port')->textInput(['maxlength' => true]) ?>

            <!-- attribute type -->
            <?= $form->field($mailer, 'encryption')->textInput(['maxlength' => true]) ?>

            <!-- attribute skip_ssl_validation -->
            <?= $form->field($mailer, 'skip_ssl_validation')->textInput(['maxlength' => true]) ?>

            <!-- attribute address -->
            <?= $form->field($mailer, 'from')->textInput(['maxlength' => true]) ?>

        </p>
        <?php $this->endBlock(); ?>

        <?=
        Tabs::widget(
            [
                'encodeLabels' => false,
                'items' => [
                    [
                        'label' => Yii::t('models', 'SuperDomain'),
                        'content' => $this->blocks['main'],
                        'active' => true,
                    ],
                    [
                        'label' => Yii::t('models', 'Mailer'),
                        'content' => $this->blocks['mailer'],
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


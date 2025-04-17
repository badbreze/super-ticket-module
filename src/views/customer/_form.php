<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Tabs;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\grid\GridView;

/**
 * @var yii\web\View $this
 * @var super\ticket\models\SuperCustomer $model
 * @var yii\widgets\ActiveForm $form
 */

$domainsDataProvider = new \yii\data\ActiveDataProvider(
    [
        'query' => $model->getDomains(),
        'pagination' => false
    ]
);


if (isset($actionColumnTemplates)) {
    $actionColumnTemplate = implode(' ', $actionColumnTemplates);
    $actionColumnTemplateString = $actionColumnTemplate;
} else {
    Yii::$app->view->params['pageButtons'] = Html::a(
        '<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('app', 'New'), ['create'],
        ['class' => 'btn btn-success']
    );
    $actionColumnTemplateString = "{view} {update} {delete}";
}
$actionColumnTemplateString = '<div class="action-buttons">' . $actionColumnTemplateString . '</div>';

?>

<div class="super-customer-form">

    <?php $form = ActiveForm::begin([
            'id' => 'SuperCustomer',
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

            <!-- attribute description -->
            <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

        </p>
        <?php $this->endBlock(); ?>

        <?php $this->beginBlock('domains'); ?>
            <a href="<?= Url::to(['/super/domain/create', 'customer_id' => $model->id]); ?>" class="float-right mt-4">
                <i class="fa fa-plus"></i>
                <?= Yii::t('super', 'Add Domain'); ?>
            </a>

            <?php \yii\widgets\Pjax::begin(['id' => 'member-list']); ?>
            <div class="table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $domainsDataProvider,
                    'columns' => [
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'controller' => 'domain',
                            'buttons' => [
                                'view' => function ($url, $model, $key) {
                                    $options = [
                                        'title' => Yii::t('app', 'View'),
                                        'aria-label' => Yii::t('app', 'View'),
                                        'data-pjax' => '0',
                                    ];
                                    return Html::a(
                                        '<span class="glyphicon glyphicon-eye-open"></span>',
                                        $url,
                                        $options
                                    );
                                }
                            ],
                            'contentOptions' => ['nowrap' => 'nowrap']
                        ],
                        'name',
                        'description',
                        [
                            'label' => Yii::t('super', 'Mailer Enabled'),
                            'value' => 'mailer.enabled',
                            'format' => 'boolean',
                        ],
                        'mailer.from',
                    ]
                ]); ?>
            </div>
            <?php \yii\widgets\Pjax::end(); ?>
        <?php $this->endBlock(); ?>

        <?=
        Tabs::widget(
            [
                'encodeLabels' => false,
                'items' => [
                    [
                        'label' => Yii::t('models', 'Customer'),
                        'content' => $this->blocks['main'],
                        'active' => true,
                    ],
                    [
                        'label' => Yii::t('models', 'Domains'),
                        'content' => $this->blocks['domains'],
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
            ($model->isNewRecord ? Yii::t('super', 'Create') : Yii::t('super', 'Save')),
            [
                'id' => 'save-' . $model->formName(),
                'class' => 'btn btn-success'
            ]
        );
        ?>

        <?php ActiveForm::end(); ?>

    </div>

</div>


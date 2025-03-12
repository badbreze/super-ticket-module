<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 */

$this->title = Yii::t('models', 'Super Customer');
$this->params['breadcrumbs'][] = $this->title;

if (isset($actionColumnTemplates)) {
    $actionColumnTemplate = implode(' ', $actionColumnTemplates);
    $actionColumnTemplateString = $actionColumnTemplate;
} else {
    Yii::$app->view->params['pageButtons'] = Html::a(
        '<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('super', 'New'), ['create'],
        ['class' => 'btn btn-success']
    );
    $actionColumnTemplateString = "{view} {update} {delete}";
}
$actionColumnTemplateString = '<div class="action-buttons">' . $actionColumnTemplateString . '</div>';
?>
<div class="giiant-crud super-customer-index">

    <?php
    //         ?>


    <?php \yii\widgets\Pjax::begin(
        [
            'id' => 'pjax-main',
            'enableReplaceState' => false,
            'linkSelector' => '#pjax-main ul.pagination a, th a',
            'clientOptions' => ['pjax:success' => 'function(){alert("yo")}']
        ]
    ) ?>

    <h1>
        <?= Yii::t('models.plural', 'Super Customer') ?>
        <small>
            <?= Yii::t('super', 'List') ?>
        </small>
    </h1>
    <div class="clearfix crud-navigation">
        <div class="pull-left">
            <?= Html::a(
                '<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('super', 'New'),
                ['create'],
                ['class' => 'btn btn-success']
            ) ?>
        </div>
    </div>

    <hr/>

    <div class="table-responsive">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'pager' => [
                'class' => yii\widgets\LinkPager::className(),
                'firstPageLabel' => Yii::t('super', 'First'),
                'lastPageLabel' => Yii::t('super', 'Last'),
            ],
            'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
            'headerRowOptions' => ['class' => 'x'],
            'columns' => [
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => $actionColumnTemplateString,
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            $options = [
                                'title' => Yii::t('super', 'View'),
                                'aria-label' => Yii::t('super', 'View'),
                                'data-pjax' => '0',
                            ];
                            return Html::a(
                                '<span class="glyphicon glyphicon-eye-open"></span>',
                                $url,
                                $options
                            );
                        }
                    ],
                    'urlCreator' => function ($action, $model, $key, $index) {
                        // using the column name as key, not mapping to 'id' like the standard generator
                        $params = is_array($key) ? $key : [
                            $model->primaryKey()[0] => (string)$key
                        ];
                        $params[0] = \Yii::$app->controller->id ? \Yii::$app->controller->id . '/' . $action : $action;
                        return Url::toRoute($params);
                    },
                    'contentOptions' => ['nowrap' => 'nowrap']
                ],
                'name',
                'description',
                [
                    'label' => Yii::t('super', 'Domains'),
                    'value' => function ($model) {
                        return count($model->domains);
                    }
                ],
            ]
        ]); ?>
    </div>

</div>


<?php \yii\widgets\Pjax::end() ?>



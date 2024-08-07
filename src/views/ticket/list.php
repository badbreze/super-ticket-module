<?php

use super\ticket\helpers\RouteHelper;
use yii\grid\GridView;
use yii\helpers\Html;

/**
 * @var $this \yii\web\View
 * @var $tickets \super\ticket\models\SuperTicket[]
 * @var $dataProvider \yii\data\ActiveDataProvider
 */
\super\ticket\assets\EnjoyAsset::register($this);
?>

<div class="row g-0">
    <?= $this->render('../parts/navigator'); ?>
    <div class="col-sm-10 p-4">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'pager' => [
                'class' => yii\widgets\LinkPager::className(),
                'firstPageLabel' => Yii::t('app', 'First'),
                'lastPageLabel' => Yii::t('app', 'Last'),
            ],
            'tableOptions' => ['class' => 'table table-hover table-hover'],
            'headerRowOptions' => ['class' => 'x'],
            'columns' => [
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    //'template' => $actionColumnTemplateString,
                ],
                [
                    'class' => \yii\grid\DataColumn::class, // this line is optional
                    'attribute' => 'user',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return Yii::$app->view->render('parts/grid_user', ['model' => $model]);
                    },
                    'label' => Yii::t('super', 'Author'),
                ],
                [
                    'class' => \yii\grid\DataColumn::class, // this line is optional
                    'attribute' => 'subject',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return Html::a($model->subject, RouteHelper::toTicket($model->id)); // your url here
                    },
                    'label' => Yii::t('super', 'Subject'),
                ],
                [
                    'class' => \yii\grid\DataColumn::class, // this line is optional
                    'attribute' => 'agent',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return Yii::$app->view->render('parts/grid_user', ['model' => $model]);
                    },
                    'label' => Yii::t('super', 'Assignee'),
                ],
                [
                    'class' => \yii\grid\DataColumn::class, // this line is optional
                    'attribute' => 'priority.name',
                    'format' => 'text',
                    'label' => Yii::t('super', 'Priority'),
                ],
                [
                    'class' => \yii\grid\DataColumn::class, // this line is optional
                    'attribute' => 'due_date',
                    'format' => 'text',
                    'label' => Yii::t('super', 'Deadline'),
                ],
                [
                    'class' => \yii\grid\DataColumn::class, // this line is optional
                    'attribute' => 'created_at',
                    'format' => 'text',
                    'label' => Yii::t('super', 'Creation'),
                ],
                [
                    'class' => \yii\grid\DataColumn::class, // this line is optional
                    'attribute' => 'lastEvent.created_at',
                    'format' => 'text',
                    'label' => Yii::t('super', 'Last Update'),
                ],
            ]
        ]); ?>
    </div>
</div>
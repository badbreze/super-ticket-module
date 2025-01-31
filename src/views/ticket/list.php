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


        <div class="nav-item dropdown dropleft float-right">
            <a href="#" class="btn btn-outline-dark" data-toggle="dropdown">
                <i class="fas fa-ellipsis-h"></i>
            </a>

            <div class="dropdown-menu shadow-sm border-0" style="text-align: center">
                <a class="dropdown-item" href="#" title="esci" data-method="post" tabindex="-1">
                   Elimina
                </a>
            </div>
        </div>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => "{summary}\n{pager}\n{items}\n{pager}",
            'pager' => [
                'class' => yii\widgets\LinkPager::className(),
                'firstPageLabel' => Yii::t('app', 'First'),
                'lastPageLabel' => Yii::t('app', 'Last'),
                'options' => ['class' => 'pagination ticket-pager'],
                'hideOnSinglePage' => false
            ],
            'tableOptions' => ['class' => 'table table-hover table-hover ticket-list'],
            'headerRowOptions' => ['class' => 'x'],
            'rowOptions' => function ($model, $key, $index, $grid) {
                /**@var $model \super\ticket\models\SuperTicket * */
                if (
                    $model->isDueDateElapsed &&
                    $model->status->identifier == \super\ticket\models\SuperTicketStatus::STATUS_OPEN
                ) {
                    return ['class' => 'bg-expired'];
                }
            },
            'columns' => [
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    //'template' => $actionColumnTemplateString,
                ],
                [
                    'class' => \yii\grid\DataColumn::class, // this line is optional
                    'attribute' => 'subject',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return Yii::$app->view->render('parts/grid_subject', ['model' => $model]);
                    },
                    'label' => Yii::t('super', 'Subject'),
                ],
                [
                    'class' => \yii\grid\DataColumn::class, // this line is optional
                    'attribute' => 'agent',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return Yii::$app->view->render('parts/grid_user', ['model' => $model->agent]);
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
                /*[
                    'class' => \yii\grid\DataColumn::class, // this line is optional
                    'attribute' => 'comments',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->getComments()->count(); // your url here
                    },
                    'label' => Yii::t('super', 'Comments'),
                ],*/
            ]
        ]); ?>
    </div>
</div>
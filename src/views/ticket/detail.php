<?php

use kartik\editors\Summernote;
use dosamigos\tinymce\TinyMce;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use super\ticket\helpers\TicketHelper;
use super\ticket\helpers\StatusHelper;
use super\ticket\helpers\RouteHelper;
use super\ticket\helpers\HtmlHelper;

$statuses = StatusHelper::getAvailableStatuses();

/**
 * @var $this \yii\web\View
 * @var $ticket \super\ticket\models\SuperTicket
 * @var $commentModel \super\ticket\models\forms\TicketCommentForm
 */

?>
<?php /* $this->render('parts/header', ['ticket' => $ticket]);*/ ?>
<div class="row g-0">
    <?php /* $this->render('../parts/navigator'); */ ?>
    <?= $this->render('parts/info', [
            'ticket' => $ticket,
    ]); ?>
    <div class="ticket-main-content col-sm-10 p-4">
        <div class="d-table-row">
            <a href="<?= \yii\helpers\Url::previous(); ?>" class="btn btn-outline-dark d-table-cell">
                <i class="fa fa-arrow-left"></i>
            </a>
            <div class="d-table-cell">
                <h4 class="card-title ticket-title m-0 ml-3 pb-2">
                    <?= strip_tags($ticket->subject); ?>
                </h4>
            </div>
        </div>
        <p class="card-description mt-3">
            Opened by <code><?= $ticket->user->fullName; ?></code> in <?= $ticket->created_at; ?>
        </p>
        <div id="ticket_thread">
            <div id="thread-items">
                <blockquote class="blockquote ticket-content-container">
                    <p class="mb-0">
                        <?= HtmlHelper::fullClean($ticket->content); ?>
                    </p>
                </blockquote>
                <?= $this->render('parts/relations', ['ticket' => $ticket]); ?>
                <?php foreach ($ticket->events as $event): ?>
                    <div class="mb-3">
                        <?= \super\ticket\helpers\EventHelper::renderEvent($event); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="ticket-comment-area">
            <div class="mt-4">
                <h5 class="ticket-comment-area-title">
                    <i class="far fa-comments"></i>
                    <?= Yii::t('super', 'Write a Comment'); ?>
                </h5>
            </div>
            <?php
            $form = ActiveForm::begin([
                                          'id' => 'comment-form',
                                          //'layout' => 'horizontal',
                                          'action' => ['/super/ticket/comment', 'ticket_id' => $ticket->id],
                                      ]);
            ?>
            <?= $form->field($commentModel, 'body')->widget(TinyMce::className(), [
                //'name' => 'test',
                'options' => ['rows' => 7],
                //'language' => 'en_GB',
                'clientOptions' => [
                    'menubar' => false,
                    'statusbar' => false,
                    'toolbar' => 'undo redo | formatselect | bold italic',
                ]
            ])->label(false); ?>
            <?= Html::submitButton(Yii::t('app', 'Send'), ['class' => 'btn btn-primary']) ?>
            <?= Html::button(Yii::t('app', 'Send to Intranet'), ['class' => 'btn btn-info']) ?>
            <?php
            ActiveForm::end();
            ?>
        </div>
    </div>
</div>
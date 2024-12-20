<?php

use dosamigos\tinymce\TinyMce;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use super\ticket\helpers\StatusHelper;
use super\ticket\helpers\HtmlHelper;
use yii\helpers\ArrayHelper;
use conquer\select2\Select2Widget;

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

        <a href="#" class="btn btn-outline-dark float-right">
            <i class="fas fa-ellipsis-h"></i>
        </a>

        <p class="card-description mt-3">
            <?= Yii::t('super', 'Opened by <code>{name}</code> in {date}', [
                'name' => $ticket->superUser->fullName,
                'date' => $ticket->created_at
            ]); ?>
        </p>
        <div id="ticket_thread">
            <div id="thread-items">
                <blockquote class="blockquote ticket-content-container">
                    <p class="mb-0">
                        <?= HtmlHelper::fullClean($ticket->content); ?>
                    </p>
                </blockquote>
                <div class="attachments">
                    <?php
                    foreach ($ticket->attachments as $attachment) {
                        echo $this->render('parts/attach_link', ['attachment' => $attachment]);
                    }
                    ?>
                </div>
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
                //'enableAjaxValidation' => true,
                //'layout' => 'horizontal',
                'action' => ['/super/ticket/comment', 'ticket_id' => $ticket->id],
            ]);
            ?>

            <?= $form->field($commentModel, 'recipients')->widget(
                Select2Widget::className(),
                [
                    'items' => ArrayHelper::map($ticket->followers, 'super_user_id', 'superUser.fullName'),
                    'multiple' => true,
                    'bootstrap' => false
                ]
            ); ?>

            <?= $form->field($commentModel, 'body')->widget(TinyMce::className(), [
                //'name' => 'test',
                'options' => ['rows' => 7],
                //'language' => 'en_GB',
                'clientOptions' => [
                    'menubar' => false,
                    'statusbar' => false,
                    'toolbar' => 'undo redo | formatselect | bold italic',
                ]
            ]); ?>

            <?php /* $form->field($commentModel, 'attachments')->fileInput([
                'options' => [
                    'multiple' => true,
                    'placeholder' => ''
                ],
                'pluginOptions' => [
                    'maxFileCount' => 5,
                    'showPreview' => false,
                    'msgPlaceholder' => Yii::t('super', "Nessun allegato inserito")
                ]
            ])->label(false);*/ ?>

            <?= Html::submitButton(Yii::t('app', 'Send'), ['class' => 'btn btn-primary']) ?>
            <?php /* Html::button(Yii::t('app', 'Send to Intranet'), ['class' => 'btn btn-info'])*/ ?>
            <?php
            ActiveForm::end();
            ?>
        </div>
    </div>
</div>
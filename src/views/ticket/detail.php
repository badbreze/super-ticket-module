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

            <div class="dropdown">
                <button type="button" class="btn btn-outline-dark float-right"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-h"></i>
                </button>
                <div class="dropdown-menu" aria-labelledby="chengeStatusButton">
                        <a href="#linkModal" class="dropdown-item">
                            <i class="mdi mdi-file-pdf text-primary"></i>
                            <?= Yii::t('super', 'External Link'); ?>
                        </a> <!-- dropdown-item -->
                </div> <!-- dropdown-menu -->
            </div> <!-- dropdown -->

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
                    <div class="mt-4">
                        <?php foreach ($ticket->events as $event): ?>
                            <div class="mb-3">
                                <?= \super\ticket\helpers\EventHelper::renderEvent($event); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
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
                    'options' => ['enctype' => 'multipart/form-data'], // important
                    'action' => ['/super/ticket/comment', 'ticket_id' => $ticket->id],
                ]);
                ?>

                <?php \yii\widgets\Pjax::begin(['id' => 'pjax_recipients']); ?>
                <div class="form-row">
                    <div class="col-11">
                        <?= $form->field($commentModel, 'recipients')->widget(
                            Select2Widget::className(),
                            [
                                'items' => ArrayHelper::map($ticket->followable, 'id', 'fullName'),
                                'multiple' => true,
                                'bootstrap' => false
                            ]
                        ); ?>
                    </div>

                    <a href="#" class="col mt-4" data-toggle="modal" data-target="#recipient-modal">
                        <i class="fa fa-plus"></i>
                        <?= Yii::t('super', 'Add Recipient'); ?>
                    </a>
                </div>
                <?php \yii\widgets\Pjax::end(); ?>

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

                <?= $form->field($commentModel, 'attachments')->fileInput([
                    'options' => [
                        'multiple' => true,
                        'placeholder' => ''
                    ],
                    'pluginOptions' => [
                        'maxFileCount' => 5,
                        'showPreview' => false,
                        'msgPlaceholder' => Yii::t('super', "Nessun allegato inserito")
                    ]
                ])->label(false); ?>

                <?= Html::submitButton(Yii::t('super', 'Send'), [
                    'class' => 'btn btn-primary',
                    'name' => 'TicketCommentForm[type]',
                    'value' => \super\ticket\models\SuperTicketEvent::TYPE_COMMENT,
                ]) ?>
                <?= Html::submitButton(Yii::t('super', 'Internal Note'), [
                        'class' => 'btn btn-info', 'id' => 'save-note',
                        'name' => 'TicketCommentForm[type]',
                    'value' => \super\ticket\models\SuperTicketEvent::TYPE_NOTE,
                ]) ?>
                <?php /* Html::button(Yii::t('app', 'Send to Intranet'), ['class' => 'btn btn-info'])*/ ?>
                <?php
                ActiveForm::end();
                ?>
            </div>
        </div>
    </div>

<?php \yii\bootstrap4\Modal::begin(['id' => 'recipient-modal', 'title' => Yii::t('super', 'Add Recipient')]); ?>
<?= $this->render('parts/modal_recipient', ['model' => new \super\ticket\models\SuperUser(),
    'ticket' => $ticket,]); ?>
<?php \yii\bootstrap4\Modal::end(); ?>
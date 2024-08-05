<?php
use super\ticket\helpers\HtmlHelper;

/**
 * @var $this \yii\web\View
 * @var $event \super\ticket\models\SuperTicketEvent
 */
?>
<div class="ticket-comment-single">
    <div class="ticket-comment-info">
        <?php if($event->creator) : ?>
        <div class="ticket-comment-avatar">
            <img class="avatar" src="<?= $event->creator->profile->getAvatar()->getUrl(
                false,
                false,
                true
            ); ?>" />
        </div>
        <?php endif; ?>
        <div class="d-inline-block">
            <?= Yii::t('super', 'In'); ?>
            <?= $event->created_at; ?>
            <code><?= $event->superUser->fullName; ?></code>
            <?= Yii::t('super', 'wrote'); ?>
        </div>
        <hr class="mt-1" />
    </div>
    <blockquote class="ticket-comment-body">
        <?= HtmlHelper::clean($event->body); ?>
    </blockquote>

    <div class="attachments">
        <?php
        foreach ($event->attachments as $attachment) {
            echo $this->render('attach_link', ['attachment' => $attachment]);
        }
        ?>
    </div>
</div>
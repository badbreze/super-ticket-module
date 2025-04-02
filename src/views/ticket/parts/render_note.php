<?php
use super\ticket\helpers\HtmlHelper;

/**
 * @var $this \yii\web\View
 * @var $event \super\ticket\models\SuperTicketEvent
 */
?>
<div class="ticket-note-single">
    <div class="ticket-note-info">
        <?php if($event->creator && $event->creator->user->profile) : ?>
            <div class="ticket-note-avatar">
                <img class="avatar" src="<?= $event->creator->user->profile->getAvatar()->getUrl(
                    false,
                    false,
                    true
                ); ?>" />
            </div>
        <?php else: ?>
            <div class="ticket-note-avatar">
                <img class="avatar" src="https://www.gravatar.com/avatar/<?= md5(
                    $event->superUser->email
                ); ?>?d=mp" />
            </div>
        <?php endif; ?>
        <div class="d-inline-block">
            <?= Yii::t('super', 'Internal note by <code>{name}</code>', [
                'name' => $event->superUser->fullName,
                'date' => $event->created_at
            ]); ?>
        </div>
        <i class="fa-solid fa-circle-info" alt="Test"></i>
        <div class="mt-1">
            <br/>
        </div>
    </div>
    <blockquote class="ticket-note-body">
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
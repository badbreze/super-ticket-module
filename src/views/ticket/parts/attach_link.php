<?php
use super\ticket\helpers\HtmlHelper;

/**
 * @var $this \yii\web\View
 * @var $attachment \elitedivision\amos\attachments\models\File
 */
?>
<div class="ticket-file-element">
    <b class="file-element">
        <div class="file-icon" data-type="<?= $attachment->type; ?>"></div>
        <?= $attachment->name.'.'.$attachment->type; ?>
    </b>
    <a href="<?= $attachment->getUrl(); ?>">Scarica</a>
</div>
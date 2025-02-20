<?php
use super\ticket\helpers\HtmlHelper;

/**
 * @var $this \yii\web\View
 * @var $attachment \elitedivision\amos\attachments\models\File
 */
?>
<div class="card" style="display: inline-block">
    <?php if(in_array($attachment->type, ['jpg','jpeg', 'png', 'gif'])) : ?>
        <img class="card-img-top ticket-image-preview" src="<?= $attachment->getUrl(); ?>" alt="<?= $attachment->name.'.'.$attachment->type; ?>"/>
        <p class="card-text" style="position:absolute;top: 0;background-color: rgba(0,0,0,0.4);color: white;padding: 4px;overflow-wrap: anywhere;">
            <?= $attachment->name.'.'.$attachment->type; ?>
        </p>
    <?php else: ?>
        <b class="file-element d-block" style="text-align: center;margin-top: 10px;">
            <div class="file-icon" data-type="<?= $attachment->type; ?>"></div>
        </b>
        <p class="card-text">
            <?= $attachment->name.'.'.$attachment->type; ?>
        </p>
    <?php endif; ?>
    <div class="card-body">
        <a href="<?= $attachment->getUrl(); ?>" target="_blank">Scarica</a>
    </div>
</div>
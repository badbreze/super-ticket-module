<?php

use yii\web\View;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/**
 * @var $this View
 * @var $model \super\ticket\models\forms\SuperTicketBulkForm
 */

$i18n_must_select = Yii::t('super', 'You Must select at least one item');

$js = <<<JS
jQuery(document).ready(function () {
    jQuery('body').on('beforeSubmit', 'form#recipient-form', function () {
        var form = jQuery(this);
        var grid = jQuery('#ticket-grid');
        
        // return false if form still have some validation errors
        if (form.find('.has-error').length) 
        {
            return false;
        }
        
        if(jQuery('.ticket-selection:checked', grid).length === 0) {
            alert("$i18n_must_select");
            jQuery('#bulk-edit-modal').modal('hide');
            return false;
        }
        
        var data = form.serialize();
        data += "&" + jQuery('.ticket-selection:checked', grid).serialize();
        
        // submit form
        jQuery.ajax({
            url    : form.attr('action'),
            type   : 'post',
            data   : data,
            success: function (response) {
                if(response == true) {
                    window.location.reload();
                }
            },
            error  : function () {
                console.log('internal server error');
            }
        });
        
        return false;
    });
});
JS;

$this->registerJs($js);
?>
<div class="super-invte-form">

    <?php $form = ActiveForm::begin([
            'id' => 'recipient-form',
            'options' => ['data-pjax' => true],
            'action' => ['/super/api/ticket/link-content']
        ]
    );
    ?>

    <div class="">
        <p>
            <select>
                <?php foreach (\Yii::$app->controller->module->externalLinking as $id=>$config): ?>
                    <option><?= Yii::t('super', 'Select One...') ?></option>
                    <option value="<?= $id; ?>"><?= $config['name']; ?></option>
                <?php endforeach; ?>
            </select>

        </p>

        <?php echo $form->errorSummary($model); ?>

        <?= Html::submitButton(
            '<span class="glyphicon glyphicon-check"></span> ' .
            Yii::t('super', 'Confirm'),
            [
                'id' => 'save-' . $model->formName(),
                'class' => 'btn btn-success'
            ]
        );
        ?>

        <?php ActiveForm::end(); ?>

    </div>
</div>
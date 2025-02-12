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
    jQuery('#bulk-delete-modal').on('show.bs.modal', function (event) {
        var checkboxes = jQuery('#ticket-grid .ticket-selection:checked');
        jQuery('#bulk-delete-list').empty();
        
        if(checkboxes.length === 0) {
            alert("$i18n_must_select");
            jQuery('#bulk-delete-modal').modal('hide');
            return false;
        }
        
        jQuery.each(checkboxes, function (index, value) {
            var checkbox = jQuery(value);
            jQuery('#bulk-delete-list').append(jQuery('<li>').text(checkbox.attr('data-title')));
        })
    });
    
    jQuery('#bulk-delete-confirm').on('click', function () {
        var grid = jQuery('#ticket-grid');
        
        if(jQuery('.ticket-selection:checked', grid).length === 0) {
            alert("$i18n_must_select");
            jQuery('#bulk-edit-modal').modal('hide');
            return false;
        }
        
        // submit form
        jQuery.ajax({
            url    : '/super/api/ticket/bulk-delete',
            type   : 'post',
            data   : jQuery('.ticket-selection:checked', grid).serialize(),
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
<div class="bulk-delete">
    <h6><?= Yii::t('super', 'You\'re about to delete tickets, this operation is irreversible'); ?></h6>

    <ul id="bulk-delete-list">
        <!-- list tickets -->
    </ul>

    <?= Html::a(Yii::t('super', 'Delete'), '#', ['class' => 'btn btn-danger', 'id' => 'bulk-delete-confirm']) ?>
</div>
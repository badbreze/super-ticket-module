<?php
use super\ticket\helpers\TicketHelper;
use super\ticket\helpers\DomainHelper;
use super\ticket\helpers\StatusHelper;
use super\ticket\models\SuperTicket;
use yii\web\View;

/**
 * @var $this View
 * @var $ticket SuperTicket
 */

$currentDomain = DomainHelper::getCurrentDomain();

$js = <<<JS
jQuery('.status-changer-element').on('click', function() {
    let id = jQuery(this).attr('data-status_id');
    
    
});
JS;

$this->registerJs($js);
?>
<div class="row g-0">
    <div class="col-sm-6">
        <div class="d-flex align-items-center justify-content-md-end">

        </div>
    </div>
</div>
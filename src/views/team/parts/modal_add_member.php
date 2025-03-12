<?php

use yii\web\View;
use yii\helpers\ArrayHelper;
use conquer\select2\Select2Widget;

/**
 * @var $this View
 * @var $model \super\ticket\models\SuperTeam
 */

$js = <<<JS
jQuery('#add-member-btn').on('click', function() {
    var items = jQuery('#add-member-form').val();
    var data = {
        members: items
    };
    
    jQuery.ajax({
        url    : '/super/api/teams/add-member?team_id={$model->id}',
        type   : 'post',
        data   : data,
        success: function (response) {
            if(response == true) {
                //Close modal
                jQuery('#members-modal').modal('hide');
                
                //Reload member form
                jQuery.pjax.reload({container: '#member-list'});
            }
        },
        error  : function () {
            console.log('internal server error');
        }});
});
JS;

$this->registerJs($js);
?>

<div class="row">
    <div class="col-12 mb-3">
        <?= Select2Widget::widget(
            [
                'name' => 'team-member',
                'id' => 'add-member-form',
                'items' => ArrayHelper::map(\super\ticket\models\SuperUser::find()->all(), 'id', 'fullName'),
                'multiple' => true,
                'bootstrap' => false,
                'settings' => [
                    'dropdownParent' => '#members-modal',
                    'dropdownAutoWidth' => true,
                    'width' => '100%'
                ]
            ]); ?>
    </div>
</div>

<a class="btn btn-info " id="add-member-btn">
    <?= Yii::t('super', 'Add'); ?>
</a>
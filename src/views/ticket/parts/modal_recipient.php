<?php

use yii\web\View;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/**
 * @var $this View
 * @var $model \super\ticket\models\SuperUser
 * @var $ticket \super\ticket\models\SuperTicket
 */

$addRecipientUrl = \yii\helpers\Url::to(['/super/api/ticket/add-recipient', 'ticket_id' => $ticket->id]);

//Ajax call to add memeber
$js = <<<JS
jQuery('#save-recipient').on('click', function(e) {
    //Prevent default submit
    e.preventDefault();
    
    var form = jQuery('#recipient-form');
    var formData = form.serialize();
    var url = form.attr('action');
    
    jQuery.ajax({
        url: '$addRecipientUrl',
        type: 'post',
        data: formData,
        success: function (response) {
            if(response != false) {
                //Close modal
                jQuery('#recipient-modal').modal('hide');
                
                //Reset Form
                form.trigger('reset');
                
                //Reload member form
                jQuery.pjax.reload({container: '#pjax_recipients'});
            }
        },
        error: function () {
            console.log('internal server error');
        }
    });
    
    return false;
});
JS;

$this->registerJs($js);
?>

<?php if ($model->id) : ?>
    <div class="alert alert-success" role="alert">
        <?= Yii::t('super', 'Recipient Invited'); ?>
    </div>

    <a class="btn btn-success " data-toggle="modal" data-target="#recipient-modal">
        <?= Yii::t('super', 'Close'); ?>
    </a>
<?php else : ?>
    <div class="super-invte-form">

        <?php $form = ActiveForm::begin([
                'id' => 'recipient-form',
                'options' => ['data-pjax' => true],
                //'action' => ['/super/ticket/add-recipient', 'ticket_id' => $ticket->id]
            ]
        );
        ?>

        <div class="">
            <p>

                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'surname')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

            </p>

            <?php echo $form->errorSummary($model); ?>

            <?= Html::button(
                '<span class="glyphicon glyphicon-check"></span> ' .
                Yii::t('super', 'Add'),
                [
                    'id' => 'save-recipient',
                    'class' => 'btn btn-success'
                ]
            );
            ?>

            <?php ActiveForm::end(); ?>

        </div>

    </div>
<?php endif; ?>
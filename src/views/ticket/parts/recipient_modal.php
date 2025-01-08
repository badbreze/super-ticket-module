<?php
use yii\web\View;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/**
 * @var $this View
 * @var $model \super\ticket\models\SuperUser
 * @var $ticket \super\ticket\models\SuperTicket
 */

\yii\widgets\Pjax::begin(['id' => 'pjax_add_recipient']);
?>
    <?php if($model->id) : ?>
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
                    'options' => ['data-pjax' => true ],
                    'action' => ['/super/ticket/add-recipient', 'ticket_id' => $ticket->id]
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

                <?= Html::submitButton(
                    '<span class="glyphicon glyphicon-check"></span> ' .
                    Yii::t('super', 'Add'),
                    [
                        'id' => 'save-' . $model->formName(),
                        'class' => 'btn btn-success'
                    ]
                );
                ?>

                <?php ActiveForm::end(); ?>

            </div>

        </div>
    <?php endif; ?>
<?php
\yii\widgets\Pjax::end();
?>
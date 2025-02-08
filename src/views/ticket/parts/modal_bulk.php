<?php

use yii\web\View;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/**
 * @var $this View
 * @var $model \super\ticket\models\forms\SuperTicketBulkForm
 */

\yii\widgets\Pjax::begin(['id' => 'pjax_bulk_edit']);
?>
    <div class="super-invte-form">

        <?php $form = ActiveForm::begin([
                'id' => 'recipient-form',
                'options' => ['data-pjax' => true],
                'action' => ['/super/ticket/bulk-edit']
            ]
        );
        ?>

        <div class="">
            <p>

                <?= // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::activeField
                $form->field($model, 'status')->dropDownList(
                    \yii\helpers\ArrayHelper::map($model->getAvailableStatuses(), 'id', 'statusName'),
                    [
                        'prompt' => Yii::t('app', 'Select'),
                        'disabled' => (isset($relAttributes) && isset($relAttributes['agent_id'])),
                    ]
                ); ?>

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
<?php
\yii\widgets\Pjax::end();
?>
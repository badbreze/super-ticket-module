<?php
namespace super\ticket\models\forms;

use super\ticket\models\SuperTicketStatus;
use Yii;
use yii\base\Model;

/**
 * Login form
 */
class SuperTicketBulkForm extends Model
{
    public $status;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'required'],
        ];
    }

    //TODO gestire aorrettamente gli stati disponibili
    public function getAvailableStatuses()
    {
        return SuperTicketStatus::find()->all();
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'status' => Yii::t('super', 'Status'),
        ];
    }

}
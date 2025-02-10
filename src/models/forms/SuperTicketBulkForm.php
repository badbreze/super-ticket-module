<?php
namespace super\ticket\models\forms;

use super\ticket\helpers\UserHelper;
use super\ticket\models\SuperTicketStatus;
use Yii;
use yii\base\Model;

/**
 * Login form
 */
class SuperTicketBulkForm extends Model
{
    public array $selection;
    public $status;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'selection'], 'required'],
            [['selection'], 'canUpdate'],
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
            'selection' => Yii::t('super', 'Selection'),
        ];
    }

    public function canUpdate($attribute) {
        $user = UserHelper::getCurrentUser();

        return true;
    }

}
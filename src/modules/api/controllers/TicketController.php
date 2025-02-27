<?php
namespace super\ticket\modules\api\controllers;

use super\ticket\helpers\UserHelper;
use super\ticket\models\forms\SuperTicketBulkForm;
use super\ticket\modules\api\base\ActiveController;

/**
 * Default controller for the `super` module
 */
class TicketController extends ActiveController
{

    public $modelClass = \super\ticket\modules\api\models\SuperTicket::class;

    public function actions()
    {
        return [
            /*'index' => [
                'class' => \super\ticket\modules\api\actions\ticket\IndexAction::class,
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'view' => [
                'class' => \super\ticket\modules\api\actions\ticket\ViewAction::class,
                'modelClass' => $this->modelClass,
                //'checkAccess' => [$this, 'checkAccess'],
            ],
            'create' => [
                'class' => \super\ticket\modules\api\actions\ticket\CreateAction::class,
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->createScenario,
            ],
            'update' => [
                'class' => \super\ticket\modules\api\actions\ticket\UpdateAction::class,
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->updateScenario,
            ],
            'delete' => [
                'class' => \super\ticket\modules\api\actions\ticket\DeleteAction::class,
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ],*/
        ];
    }

    public function actionIndex() {
        return "OK";
    }

    public function actionBulkEdit() {
        $model = new SuperTicketBulkForm();

        if($model->load(\Yii::$app->request->post()) && $model->validate()) {
            return $model->updateTickets();
        }

        return $model->errors;
    }

    public function actionBulkDelete() {
        $model = new SuperTicketBulkForm();

        if($model->load(\Yii::$app->request->post()) && $model->validate()) {
            return $model->deleteTickets();
        }

        return true;
    }
}

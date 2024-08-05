<?php

namespace super\ticket\controllers;

use super\ticket\base\Controller;
use super\ticket\models\SuperTicket;
use super\ticket\models\SuperUser;

/**
 * Default controller for the `super` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        //$tickets = SuperTicket::find()->all();

        return $this->render('index', [
            //'tickets' => $tickets
        ]);
    }

    public function actionInitUser()
    {
        $model = new SuperUser();

        try {
            $model->load(\Yii::$app->request->post());
            $model->user_id = \Yii::$app->user->id;

            if ($model->save()) {
                return $this->redirect(['index', 'id' => $model->id]);
            }
        } catch (\Exception $e) {
            $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            $model->addError('_exception', $msg);
        }

        return $this->render('init-user', [
            'model' => $model
        ]);
    }
}

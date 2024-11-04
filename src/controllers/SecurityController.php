<?php

namespace super\ticket\controllers;

use super\ticket\models\forms\LoginForm;
use super\ticket\base\Controller;
use super\ticket\models\SuperTicketSla;
use dmstr\bootstrap\Tabs;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * Default controller for the `super` module
 */
class SecurityController extends Controller
{
    public $layout = 'spectate';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'allow' => true,
                        ],
                    ],
                ],
            ];
    }

    public function actionLogin() {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(\Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }
}

<?php
namespace super\ticket\base;

use super\ticket\helpers\UserHelper;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * Base controller for the `super` module
 */
class Controller extends \yii\web\Controller
{
    public $layout = 'enjoy';

    public function init()
    {
        parent::init();

        if(!UserHelper::isCurrentUserAvailable()) {
            $initAction = 'super/default/init-user';

            if(\Yii::$app->requestedRoute != $initAction) {
                \Yii::$app->response->redirect("/$initAction")->send();

                exit;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['SUPER_USER'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        //'action_name' => ['post', 'get']
                    ]
                ]
            ]
        );
    }
}

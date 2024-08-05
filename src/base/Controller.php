<?php
namespace super\ticket\base;

use super\ticket\helpers\UserHelper;

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
}

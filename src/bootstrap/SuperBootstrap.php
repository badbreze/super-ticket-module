<?php

namespace super\ticket\bootstrap;

use yii\base\Application;

class SuperBootstrap implements \yii\base\BootstrapInterface
{
    /**
     * @inheritDoc
     */
    public function bootstrap($app)
    {
        \Yii::$app->urlManager
            ->addRules([
                           'o/<domain_id:[0-9]+>' => '/super/ticket/list',
                           't/<ticket_id:[0-9]+>' => '/super/ticket/detail',
                           //'work/<domain_id:[^\/]+>/<ticket_id:[^\/]+>' => '/super/ticket/detail',
                       ], false);
    }
}
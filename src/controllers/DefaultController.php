<?php

namespace super\ticket\controllers;

use super\ticket\base\Controller;
use super\ticket\models\SuperTicket;

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
        $tickets = SuperTicket::find()->all();


        \Yii::getLogger()->log('info', 'Hook ricevuto');

        return $this->render('index', [
            'tickets' => $tickets
        ]);
    }
}

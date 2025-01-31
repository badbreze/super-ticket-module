<?php

namespace super\ticket\helpers;

use super\ticket\models\SuperTicket;
use super\ticket\models\SuperTicketEvent;
use super\ticket\models\SuperTicketStatus;

class EventHelper
{
    public static function renderEvent(SuperTicketEvent $event) {
        return \Yii::$app->view->render('parts/render_'.$event->type, ['event' => $event]);
    }
}
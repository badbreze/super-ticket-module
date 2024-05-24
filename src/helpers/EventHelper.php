<?php

namespace super\ticket\helpers;

use super\ticket\models\SuperTicket;
use super\ticket\models\SuperTicketEvent;
use super\ticket\models\SuperTicketStatus;

class EventHelper
{
    public static function renderEvent(SuperTicketEvent $event) {
        switch ($event->type) {
            case SuperTicketEvent::TYPE_COMMENT: {
                return \Yii::$app->view->render('parts/render_comment', ['event' => $event]);
            }
            break;
            case SuperTicketEvent::TYPE_STATUS_CHANGE: {
                return \Yii::$app->view->render('parts/render_status', ['event' => $event]);
            }
                break;
            default:
                return \Yii::$app->view->render('parts/render_generic', ['event' => $event]);
        }
    }
}
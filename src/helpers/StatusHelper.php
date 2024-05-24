<?php

namespace super\ticket\helpers;

use super\ticket\models\SuperTicketStatus;

class StatusHelper
{

    /**
     * @return null|SuperTicketStatus[]
     * @throws \yii\base\InvalidConfigException
     */
    public static function getAvailableStatuses() {
        return SuperTicketStatus::find()->all();
    }

    /**
     * @return null|SuperTicketStatus
     * @throws \yii\base\InvalidConfigException
     */
    public static function getCurrentStatus() {
        $requestId = \Yii::$app->request->get('status_identifier');

        if($requestId) {
            return SuperTicketStatus::find()->andWhere(['identifier' => $requestId])->one();
        }

        $currentTicket = TicketHelper::getCurrentTicket();

        if ($currentTicket && $currentTicket->id) {
            return $currentTicket->status;
        }

        return null;
    }
}
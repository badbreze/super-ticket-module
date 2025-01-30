<?php
namespace super\ticket\helpers;

use super\ticket\models\SuperTicket;
use super\ticket\models\SuperTicketStatus;
use super\ticket\helpers\RouteHelper;

class TicketHelper
{
    /**
     * @return null|SuperTicket
     * @throws \yii\base\InvalidConfigException
     */
    public static function getCurrentTicket() {
        $requestId = \Yii::$app->request->get('ticket_id');

        if($requestId) {
            return SuperTicket::find()->andWhere(['id' => $requestId])->one();
        }

        return null;
    }

    public static function getListUrl($status = null, $domain = null) {
        $toState = $status ?: StatusHelper::getCurrentStatus()->identifier;
        $toDomain = $domain ?: DomainHelper::getCurrentDomain()->id;

        $defaultState = SuperTicketStatus::find()->andWhere(['id' => 1])->one();

        $toState = $toState ?: $defaultState->identifier;

        return RouteHelper::toOrganization($toDomain, $toState);
    }

    public static function getTicketDetailUrl(SuperTicket $ticket) {
        return RouteHelper::toTicket($ticket->id, $ticket->domain_id);
    }

    public static function amIFollowing(SuperTicket $ticket) {
        $user = UserHelper::getCurrentUser();

        $q = $ticket->getTicketFollowers()
            ->andWhere(['super_ticket_follower.super_user_id' => $user->id]);

        return $q->count() > 0;
    }
}
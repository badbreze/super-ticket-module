<?php

namespace super\ticket\helpers;

use super\ticket\models\SuperTicket;
use super\ticket\models\SuperTicketStatus;
use yii\helpers\Url;

class RouteHelper
{
    public static function usePrettyUrl()
    {
        //TODO rendere dinamico in base alla presenza o meno di riotte custom
        return true;
    }

    public static function toOrganization($organization_id, $status = SuperTicketStatus::STATUS_OPEN, $scheme = false)
    {
        if (self::usePrettyUrl()) {
            return Url::to(["/o/{$organization_id}", "status_identifier" => $status]);
        }

        return Url::to([
                           '/super/ticket/list',
                           'domain_id' => $organization_id,
                           'status_identifier' => $status
                       ], $scheme);
    }

    public static function toTicket($ticket_id, $organization_id = null, $scheme = false)
    {
        if (empty($organization_id)) {
            $ticket = SuperTicket::findOne(['id' => $ticket_id]);
            $organization_id = $ticket ? $ticket->domain_id : null;
        }

        if (self::usePrettyUrl()) {
            return Url::to(["/t/{$ticket_id}"], $scheme);
        }

        return Url::to([
                           '/super/ticket/detail',
                           //'domain_id' => $organization_id,
                           'ticket_id' => $ticket_id
                       ], $scheme);
    }

    public static function updateTicketAttribute($ticket_id, $attribute, $value) {
        //TODO

        return Url::to([
                           '/super/ticket/update-attribute',
                           'ticket_id' => $ticket_id,
                           'attribute' => $attribute,
                           'value' => $value,
                       ]);
    }
}
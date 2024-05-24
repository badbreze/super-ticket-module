<?php

namespace super\ticket\helpers;

use super\ticket\models\SuperDomain;

class DomainHelper
{

    /**
     * @return null|SuperDomain[]
     * @throws \yii\base\InvalidConfigException
     */
    public static function getAvailableDomains() {
        return SuperDomain::find()
            ->joinWith('customer')
            ->orderBy(['super_customer.name' => SORT_ASC, 'super_domain.name' => SORT_ASC])
            ->all();
    }

    /**
     * @return null|SuperDomain
     * @throws \yii\base\InvalidConfigException
     */
    public static function getCurrentDomain() {
        $requestId = \Yii::$app->request->get('domain_id');

        if($requestId) {
            return SuperDomain::find()->andWhere(['id' => $requestId])->one();
        }

        $currentTicket = TicketHelper::getCurrentTicket();

        if ($currentTicket && $currentTicket->id) {
            return $currentTicket->domain;
        }

        return null;
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public static function getCurrentDomainName() {
        return self::getCurrentDomain()->name;
    }
}
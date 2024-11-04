<?php

namespace super\ticket\helpers;

use super\ticket\models\SuperDomain;

class DateTimeHelper
{
    public static function compareTimeOnly(\DateTime $start, \DateTime $end) {
        $compStart = clone $start;
        $compEnd = clone $start;

        $compEnd->setTime($end->format('H'), $end->format('i'), $end->format('s'));

        return $compEnd->diff($compStart)->format('%R');
    }
}
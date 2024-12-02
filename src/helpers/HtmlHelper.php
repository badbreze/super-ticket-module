<?php

namespace super\ticket\helpers;

use yii\helpers\HtmlPurifier;

/**
 * HTML Purifier DOC
 * http://htmlpurifier.org/live/configdoc/plain.html
 */
class HtmlHelper
{
    public static function clean($html) {
        return HtmlPurifier::process($html, [
            'URI.AllowedSchemes' => array('data' => true),
            //'HTML.AllowedAttributes' => 'src, height, width, alt'
        ]);
    }

    public static function fullClean($html) {
        return HtmlPurifier::process($html, [
            'AutoFormat.DisplayLinkURI' => true
        ]);
    }
}
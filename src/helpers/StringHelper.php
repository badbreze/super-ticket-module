<?php

namespace super\ticket\helpers;

class StringHelper
{
    /**
     * This method parses a string in search of the following syntax
     *  Sample text {{varOrProp}} having {{params.obeject.property}}
     * @param $subject
     * @param $params
     * @return array|string|string[]|null
     */
    public static function parse($subject, $params = []) {
        return preg_replace_callback(
            '/\{\{\s*([\w\.\/-]+)\s*\}\}/',
            function (array $matches) use ($params) {
                $match = $matches[1];
                $elements = explode('.', $match);

                //Questa cosa fa un giro un po da sistemare, è un po astruso
                if(count($elements) > 1) {
                    $rootName = $elements[0];
                    $unroll = $$rootName;

                    //Drop Root
                    array_shift($elements);

                    foreach ($elements as $part) {
                        if (is_array($unroll) && isset($unroll[$part])) {
                            $unroll = $unroll[$part];
                        } elseif (is_object($unroll) && isset($unroll->$part)) {
                            $unroll = $unroll->$part;
                        } else {
                            return null;
                        }
                    }

                    return $unroll;
                } else {
                    return isset($$match) ? $$match : null;
                }
            },
            $subject
        );
    }
}
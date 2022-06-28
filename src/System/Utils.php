<?php

namespace App\System;

class Utils
{


    public static function textToArray(string $text)
    {
        $text = preg_replace('/([\n\t\r]+)/', "\n", $text);
        return array_filter(explode("\n", $text),fn($subtext) => !empty($subtext));
    }

    public static function arrayToText(array $array_text)
    {
        return implode("\n",$array_text);
    }

    public static function prepareDescriptionToBase(string $desc)
    {
        return json_encode(self::textToArray($desc));
    }

}
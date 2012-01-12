<?php

namespace Kunstmaan\AdminBundle\Modules;

class Slugifier
{
    public static function slugify($text)
    {
        $text = preg_replace('#[^\\pL\d]+#u', '-', $text); // replace non letter or digits by -
        $text = trim($text, '-'); //trim

        // transliterate
        if (function_exists('iconv')){
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        }

        $text = strtolower($text); // lowercase
        $text = preg_replace('#[^-\w]+#', '', $text); // remove unwanted characters

        if (empty($text)){
            return 'n-a';
        }

        return $text;
    }
}
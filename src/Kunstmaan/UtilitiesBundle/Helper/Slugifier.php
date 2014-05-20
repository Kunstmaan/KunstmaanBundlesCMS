<?php

namespace Kunstmaan\UtilitiesBundle\Helper;

/**
 * Sulgifier is a helper to slugify a certain string
 */
class Slugifier
{
    /**
     * Slugify a string
     *
     * @param string $text    Text to slugify
     * @param string $default Default return value (override when slugify would return an empty string)
     *
     * @return string
     */
    public static function slugify($text, $default = 'n-a')
    {
        // transliterate
        if (function_exists('iconv')) {
            $previouslocale = setlocale(LC_CTYPE, 0);
            setlocale(LC_CTYPE, 'en_US.UTF8');
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
            setlocale(LC_CTYPE, $previouslocale);
        }

        $text = preg_replace('#[^\\pL\d\/]+#u', '-', $text); // replace non letter or digits by -
        $text = trim($text, '-'); //trim

        $text = strtolower($text); // lowercase
        $text = preg_replace('#[^-\w\/]+#', '', $text); // remove unwanted characters

        if (empty($text)) {
            return empty($default) ? '' : $default;
        }

        return $text;
    }
}

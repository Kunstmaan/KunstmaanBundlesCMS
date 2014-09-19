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
    public static function slugify($text, $default = 'n-a', $replace = array("'"), $delimiter = '-')
    {
        if (!empty($replace)) {
            $text = str_replace($replace, ' ', $text);
        }

        // transliterate
        if (function_exists('iconv')) {
            $previouslocale = setlocale(LC_CTYPE, 0);
            setlocale(LC_CTYPE, 'en_US.UTF8');
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
            setlocale(LC_CTYPE, $previouslocale);
        }

        $text = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $text);
        $text = strtolower(trim($text, $delimiter));
        $text = preg_replace("/[\/_|+ -]+/", $delimiter, $text);

        if (empty($text)) {
            return empty($default) ? '' : $default;
        }

        return $text;
    }
}

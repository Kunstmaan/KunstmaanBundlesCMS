<?php

namespace Kunstmaan\AdminBundle\Helper;

class Slugifier
{
    /**
     * @param string $text    Text to slugify
     * @param string $default Default return value (override when slugify would return an empty string)
     *
     * @return string
     */
    public static function slugify($text, $default = 'n-a')
    {
        $text = preg_replace('#[^\\pL\d]+#u', '-', $text); // replace non letter or digits by -
        $text = trim($text, '-'); //trim

        // transliterate
        if (function_exists('iconv')) {
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        }

        $text = strtolower($text); // lowercase
        $text = preg_replace('#[^-\w]+#', '', $text); // remove unwanted characters

        if (empty($text)) {
            return empty($default) ? '' : $default;
        }

        return $text;
    }
}

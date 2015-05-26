<?php

namespace Kunstmaan\UtilitiesBundle\Helper;

/**
 * Sulgifier is a helper to slugify a certain string
 */
class Slugifier implements SlugifierInterface
{
    /**
     * Slugify a string
     *
     * @param string $text    Text to slugify
     * @param string $default Default return value (override when slugify would return an empty string)
     *
     * @return string
     */
    public function slugify($text, $default = 'n-a', $replace = array("'"), $delimiter = '-')
    {
        if (!empty($replace)) {
            $text = str_replace($replace, ' ', $text);
        }

        // transliterate
        if (class_exists('Transliterator')) {
            $text = mb_convert_encoding((string)$text, 'UTF-8', mb_list_encodings());

            $transliterator = \Transliterator::create('Accents-Any');
            $text = $transliterator->transliterate($text);
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

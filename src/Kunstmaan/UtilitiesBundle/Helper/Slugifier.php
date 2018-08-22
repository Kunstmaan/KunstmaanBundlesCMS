<?php

namespace Kunstmaan\UtilitiesBundle\Helper;

use Behat\Transliterator\Transliterator;

/**
 * Sulgifier is a helper to slugify a certain string
 */
final class Slugifier implements SlugifierInterface
{
    /**
     * Slugify a string
     *
     * @param string $text
     * @param string $delimiter
     *
     * @return string
     */
    public function slugify($text, $delimiter = '-')
    {
        return Transliterator::transliterate($text, $delimiter);
    }
}

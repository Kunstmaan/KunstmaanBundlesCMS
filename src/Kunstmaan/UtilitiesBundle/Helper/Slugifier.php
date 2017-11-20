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
     *
     * @return string
     */
    public function slugify($text)
    {
        return Transliterator::transliterate($text);
    }
}

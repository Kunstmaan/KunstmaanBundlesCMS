<?php

declare(strict_types=1);

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
    public function slugify(string $text, string $delimiter = '-'): string
    {
        return Transliterator::transliterate($text, $delimiter);
    }

}

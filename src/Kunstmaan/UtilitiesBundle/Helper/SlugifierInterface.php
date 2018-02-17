<?php

declare(strict_types=1);

namespace Kunstmaan\UtilitiesBundle\Helper;

/**
 * Interface SlugifierInterface
 * @package Kunstmaan\UtilitiesBundle\Helper
 */
interface SlugifierInterface
{
    /**
     * @param string $text
     * @param string $delimiter
     * @return string
     */
    public function slugify(string $text, string $delimiter = '-'): string;

}

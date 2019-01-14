<?php

namespace Kunstmaan\UtilitiesBundle\Helper;

/**
 * Interface SlugifierInterface
 */
interface SlugifierInterface
{
    /**
     * @param string $text
     * @param string $delimiter
     *
     * @return mixed
     */
    public function slugify($text, $delimiter = '-');
}

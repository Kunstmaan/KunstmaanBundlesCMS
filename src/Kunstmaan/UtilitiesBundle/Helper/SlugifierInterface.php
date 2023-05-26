<?php

namespace Kunstmaan\UtilitiesBundle\Helper;

interface SlugifierInterface
{
    /**
     * @param string $text
     * @param string $delimiter
     */
    public function slugify($text, $delimiter = '-');
}

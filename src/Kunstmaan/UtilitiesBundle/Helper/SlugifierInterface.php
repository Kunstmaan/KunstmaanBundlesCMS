<?php

namespace Kunstmaan\UtilitiesBundle\Helper;

interface SlugifierInterface
{
    /**
     * @param string $text
     * @param string $delimiter
     *
     * @return string
     */
    public function slugify($text, $delimiter = '-');
}

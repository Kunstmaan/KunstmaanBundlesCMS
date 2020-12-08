<?php

namespace Kunstmaan\UtilitiesBundle\Helper;

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

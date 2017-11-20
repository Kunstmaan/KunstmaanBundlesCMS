<?php

namespace Kunstmaan\UtilitiesBundle\Helper;


/**
 * Interface SlugifierInterface
 * @package Kunstmaan\UtilitiesBundle\Helper
 */
interface SlugifierInterface
{

    /**
     * @param $text
     * @return mixed
     */
    public function slugify($text);

}

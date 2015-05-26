<?php

namespace Kunstmaan\UtilitiesBundle\Helper;


/**
 * Interface SlugifierInterface
 * @package Kunstmaan\UtilitiesBundle\Helper
 */
interface SlugifierInterface {

    /**
     * @param $text
     * @param $default
     * @param $replace
     * @param $delimiter
     * @return mixed
     */
    public function slugify($text, $default = 'n-a', $replace = array("'"), $delimiter = '-');

}
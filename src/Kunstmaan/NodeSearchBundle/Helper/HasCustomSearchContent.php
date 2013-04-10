<?php

namespace Kunstmaan\NodeSearchBundle\Helper;

interface HasCustomSearchContent {

    /**
     * Return a string containing all additonal content to be indexed in the content field
     *
     * @return string
     */
    public function getCustomSearchContent();

}
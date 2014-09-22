<?php

namespace Kunstmaan\NodeSearchBundle\Helper;

interface SearchBoostInterface
{
    /**
     * @return float
     */
    public function getSearchBoost();
}

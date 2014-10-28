<?php

namespace Kunstmaan\NodeSearchBundle\Helper;

/**
 * Implement this interface to override the default 'boost' value for this class (to make specific entities
 * more prominent in your search results).
 */
interface SearchBoostInterface
{
    /**
     * @return float
     */
    public function getSearchBoost();
}

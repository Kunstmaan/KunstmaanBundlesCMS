<?php

namespace Kunstmaan\AdminBundle\Helper\Menu;

/**
 * A MenuItem which represents an item in the top menu
 */
class TopMenuItem extends MenuItem
{
    /**
     * @var bool
     */
    private $appearInSidebar = false;

    /**
     * @param bool $appearInSidebar
     *
     * @return TopMenuItem
     */
    public function setAppearInSidebar($appearInSidebar)
    {
        $this->appearInSidebar = $appearInSidebar;

        return $this;
    }

    /**
     * @return bool
     */
    public function getAppearInSidebar()
    {
        return $this->appearInSidebar;
    }
}

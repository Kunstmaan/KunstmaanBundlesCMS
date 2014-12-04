<?php

namespace Kunstmaan\AdminBundle\Helper\Menu;

/**
 * A MenuItem which represents an item in the top menu
 */
class TopMenuItem extends MenuItem
{
    /**
     * @var boolean
     */
    private $appearInSidebar = false;

    /**
     * @param boolean $appearInSidebar
     *
     * @return TopMenuItem
     */
    public function setAppearInSidebar($appearInSidebar)
    {
        $this->appearInSidebar = $appearInSidebar;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getAppearInSidebar()
    {
        return $this->appearInSidebar;
    }
}

<?php

namespace Kunstmaan\NodeBundle\Helper;

/**
 * Interface TreeIconInterface
 * Implement this interface to give you control over the navigation tree icon
 */
interface TreeIconInterface
{
    /**
     * @return string
     */
    public function getIcon();
}

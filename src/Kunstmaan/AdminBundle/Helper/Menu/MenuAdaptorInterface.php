<?php

namespace Kunstmaan\AdminBundle\Helper\Menu;

use Symfony\Component\HttpFoundation\Request;

/**
 * The menu adaptor can be used to configure the main menu, to do this you need to implement this interface and tag
 * your interface with 'kunstmaan_admin.menu.adaptor'
 */
interface MenuAdaptorInterface
{
    /**
     * In this method you can add children for a specific parent, but also remove and change the already created children
     *
     * @param MenuBuilder   $menu      The MenuBuilder
     * @param MenuItem[]    &$children The current children
     * @param MenuItem|null $parent    The parent Menu item
     * @param Request       $request   The Request
     */
    public function adaptChildren(MenuBuilder $menu, array &$children, MenuItem $parent = null, Request $request = null);
}

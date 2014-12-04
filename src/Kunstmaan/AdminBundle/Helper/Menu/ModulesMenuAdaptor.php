<?php

namespace Kunstmaan\AdminBundle\Helper\Menu;

use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;

use Symfony\Component\HttpFoundation\Request;

/**
 * MenuAdaptor to add the Modules MenuItem to the top menu
 */
class ModulesMenuAdaptor implements MenuAdaptorInterface
{
    /**
     * In this method you can add children for a specific parent, but also remove and change the already created children
     *
     * @param MenuBuilder $menu      The MenuBuilder
     * @param MenuItem[]  &$children The current children
     * @param MenuItem    $parent    The parent Menu item
     * @param Request     $request   The Request
     */
    public function adaptChildren(MenuBuilder $menu, array &$children, MenuItem $parent = null, Request $request = null)
    {
        if (is_null($parent)) {
            $menuItem = new TopMenuItem($menu);
            $menuItem
                ->setRoute('KunstmaanAdminBundle_modules')
                ->setLabel('Modules')
                ->setUniqueId('modules')
                ->setParent($parent);
            if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                $menuItem->setActive(true);
            }
            $children[] = $menuItem;
        }
    }

}

<?php

namespace Kunstmaan\AdminBundle\Helper\Menu;

use Symfony\Component\HttpFoundation\Request;

/**
 * MenuAdaptor to add the Modules MenuItem to the top menu
 */
class ModulesMenuAdaptor implements MenuAdaptorInterface
{
    public function adaptChildren(MenuBuilder $menu, array &$children, MenuItem $parent = null, Request $request = null)
    {
        if (\is_null($parent)) {
            $menuItem = new TopMenuItem($menu);
            $menuItem
                ->setRoute('KunstmaanAdminBundle_modules')
                ->setLabel('modules.title')
                ->setUniqueId('modules')
                ->setParent($parent);
            if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                $menuItem->setActive(true);
            }
            $children[] = $menuItem;
        }
    }
}

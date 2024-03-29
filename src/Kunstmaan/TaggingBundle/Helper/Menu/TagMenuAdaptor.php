<?php

namespace Kunstmaan\TaggingBundle\Helper\Menu;

use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;
use Symfony\Component\HttpFoundation\Request;

class TagMenuAdaptor implements MenuAdaptorInterface
{
    public function adaptChildren(MenuBuilder $menu, array &$children, ?MenuItem $parent = null, ?Request $request = null)
    {
        if (!\is_null($parent) && 'KunstmaanAdminBundle_modules' == $parent->getRoute()) {
            $menuItem = new TopMenuItem($menu);
            $menuItem
                ->setRoute('kunstmaantaggingbundle_admin_tag')
                ->setUniqueId('Tags')
                ->setLabel('Tags')
                ->setParent($parent);
            if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                $menuItem->setActive(true);
                $parent->setActive(true);
            }
            $children[] = $menuItem;
        }
    }
}

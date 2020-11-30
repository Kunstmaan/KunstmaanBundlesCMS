<?php

namespace Kunstmaan\MenuBundle\Service;

use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;
use Symfony\Component\HttpFoundation\Request;

class MenuAdaptor implements MenuAdaptorInterface
{
    /**
     * @var array
     */
    private $menuNames;

    public function __construct(array $menuNames)
    {
        $this->menuNames = $menuNames;
    }

    public function adaptChildren(MenuBuilder $menu, array &$children, MenuItem $parent = null, Request $request = null)
    {
        if ((count($this->menuNames) > 0) && null !== $parent && 'KunstmaanAdminBundle_modules' === $parent->getRoute()) {
            $menuItem = new TopMenuItem($menu);
            $menuItem
                ->setRoute('kunstmaanmenubundle_admin_menu')
                ->setUniqueId('menus')
                ->setLabel('kuma_menu.menus.title')
                ->setParent($parent);
            if ($request->attributes->get('_route') === 'kunstmaanmenubundle_admin_menu') {
                $menuItem->setActive(true);
                $parent->setActive(true);
            }
            $children[] = $menuItem;
        }
    }
}

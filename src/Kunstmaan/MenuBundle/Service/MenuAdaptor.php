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

    /**
     * @param array $menuNames
     */
    public function __construct(array $menuNames)
    {
        $this->menuNames = $menuNames;
    }

    /**
     * @param MenuBuilder $menu
     * @param array       $children
     * @param MenuItem    $parent
     * @param Request     $request
     */
    public function adaptChildren(MenuBuilder $menu, array &$children, MenuItem $parent = null, Request $request = null)
    {
        if (count($this->menuNames) > 0) {
            if (!is_null($parent) && 'KunstmaanAdminBundle_modules' == $parent->getRoute()) {
                $menuItem = new TopMenuItem($menu);
                $menuItem
                    ->setRoute('kunstmaanmenubundle_admin_menu')
                    ->setUniqueId('menus')
                    ->setLabel('kuma_menu.menus.title')
                    ->setParent($parent);
                if (in_array($request->attributes->get('_route'), array(
                    'kunstmaanmenubundle_admin_menu',
                ))) {
                    $menuItem->setActive(true);
                    $parent->setActive(true);
                }
                $children[] = $menuItem;
            }
        }
    }
}

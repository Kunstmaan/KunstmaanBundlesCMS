<?php

namespace Kunstmaan\LeadGenerationBundle\Service;

use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;
use Symfony\Component\HttpFoundation\Request;

class MenuAdaptor implements MenuAdaptorInterface
{
    public function adaptChildren(MenuBuilder $menu, array &$children, ?MenuItem $parent = null, ?Request $request = null)
    {
        if (!\is_null($parent) && 'KunstmaanAdminBundle_modules' == $parent->getRoute()) {
            $menuItem = new TopMenuItem($menu);
            $menuItem
                ->setRoute('kunstmaanleadgenerationbundle_admin_popup_abstractpopup')
                ->setUniqueId('popup')
                ->setLabel('Popups')
                ->setParent($parent);
            if ($request->attributes->get('_route') === 'kunstmaanleadgenerationbundle_admin_popup_abstractpopup') {
                $menuItem->setActive(true);
                $parent->setActive(true);
            }
            $children[] = $menuItem;
        }
    }
}

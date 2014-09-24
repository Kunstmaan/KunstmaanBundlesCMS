<?php

namespace Kunstmaan\FormBundle\Helper\Menu;

use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;

use Symfony\Component\HttpFoundation\Request;

/**
 * The Form Submissions Menu Adaptor
 */
class FormSubmissionsMenuAdaptor implements MenuAdaptorInterface
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
        if (!is_null($parent) && 'KunstmaanAdminBundle_modules' == $parent->getRoute()) {
            $menuitem = new TopMenuItem($menu);
            $menuitem
                ->setRoute('KunstmaanFormBundle_formsubmissions')
                ->setLabel('Form submissions')
                ->setUniqueId('Form submissions')
                ->setParent($parent);
            if (stripos($request->attributes->get('_route'), $menuitem->getRoute()) === 0) {
                $menuitem->setActive(true);
                $parent->setActive(true);
            }
            $children[] = $menuitem;
        }
    }
}

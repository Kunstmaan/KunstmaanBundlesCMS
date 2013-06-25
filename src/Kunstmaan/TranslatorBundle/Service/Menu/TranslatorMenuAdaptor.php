<?php

namespace Kunstmaan\TranslatorBundle\Service\Menu;

use Symfony\Component\HttpFoundation\Request;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;

class TranslatorMenuAdaptor implements MenuAdaptorInterface
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
            $children[] = $this->getTopMenuItem($menu, $request);
        }
    }

    public function getTopMenuItem(MenuBuilder $menu, Request $request = null)
    {
        $menuitem = new TopMenuItem($menu);
        $menuitem->setRoute('KunstmaanTranslatorBundle_translations_show');
        //$menuitem->setRouteparams(array('domainId' => $domain->getId()));
        $menuitem->setRouteparams(array('domainId' => 1));
        $menuitem->setInternalname('Translations');
        $menuitem->setParent(null);
        return $menuitem;
    }
}
<?php
namespace Kunstmaan\SearchBundle\Helper\Menu;

use Symfony\Component\HttpFoundation\Request;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;
use Symfony\Component\Translation\Translator;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * The Search Menu Adaptor
 */
class SearchMenuAdaptor implements MenuAdaptorInterface
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

        } else if ('KunstmaanAdminBundle_settings' == $parent->getRoute()) {
            $menuitem = new MenuItem($menu);
            $menuitem->setRoute('KunstmaanAdminBundle_settings_searches');
            $menuitem->setInternalname('Searches');
            $menuitem->setRouteparams(array());
            $menuitem->setParent($parent);
            if (stripos($request->attributes->get('_route'), "KunstmaanAdminBundle_settings_searches") === 0) {
                $menuitem->setActive(true);
            }
            $children[] = $menuitem;
        }
    }
}

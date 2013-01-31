<?php
namespace Kunstmaan\SearchBundle\Helper\Menu;

use Symfony\Component\HttpFoundation\Request;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;

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

        } elseif ('KunstmaanAdminBundle_settings' == $parent->getRoute()) {
            $menuitem = new MenuItem($menu);
            $menuitem->setRoute('KunstmaanSearchBundle_admin_searchedfor');
            $menuitem->setInternalName('Searches');
            $menuitem->setRouteparams(array());
            $menuitem->setParent($parent);
            if (stripos($request->attributes->get('_route'), "KunstmaanSearchBundle_admin_searchedfor") === 0) {
                $menuitem->setActive(true);
            }
            $children[] = $menuitem;
        }
    }
}

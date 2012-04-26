<?php
// src/Acme/DemoBundle/Menu/Builder.php
namespace Kunstmaan\AdminBundle\Helper\Menu;
use Symfony\Component\HttpFoundation\Request;

use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;

use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;

use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;

use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;

use Symfony\Component\Translation\Translator;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Knp\Menu\ItemInterface as KnpMenu;

/**
 * The Settings Menu Adaptor
 */
class SettingsMenuAdaptor implements MenuAdaptorInterface
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
            $menuitem = new TopMenuItem($menu);
            $menuitem->setRoute('KunstmaanAdminBundle_settings');
            $menuitem->setInternalname('Settings');
            $menuitem->setParent($parent);
            $menuitem->setRole("settings");
            if (stripos($request->attributes->get('_route'), $menuitem->getRoute()) === 0) {
                $menuitem->setActive(true);
            }
            $children[] = $menuitem;
        } else if ('KunstmaanAdminBundle_settings' == $parent->getRoute()) {
            $menuitem = new MenuItem($menu);
            $menuitem->setRoute('KunstmaanAdminBundle_settings_users');
            $menuitem->setInternalname('Users');
            $menuitem->setParent($parent);
            if (stripos($request->attributes->get('_route'), $menuitem->getRoute()) === 0) {
                $menuitem->setActive(true);
            }
            $children[] = $menuitem;

            $menuitem = new MenuItem($menu);
            $menuitem->setRoute('KunstmaanAdminBundle_settings_groups');
            $menuitem->setInternalname('Groups');
            $menuitem->setParent($parent);
            if (stripos($request->attributes->get('_route'), $menuitem->getRoute()) === 0) {
                $menuitem->setActive(true);
            }
            $children[] = $menuitem;

            $menuitem = new MenuItem($menu);
            $menuitem->setRoute('KunstmaanAdminBundle_settings_roles');
            $menuitem->setInternalname('Roles');
            $menuitem->setParent($parent);
            if (stripos($request->attributes->get('_route'), $menuitem->getRoute()) === 0) {
                $menuitem->setActive(true);
            }
            $children[] = $menuitem;

            $menuitem = new MenuItem($menu);
            $menuitem->setRoute('KunstmaanAdminBundle_settings_logs');
            $menuitem->setInternalname('Logs');
            $menuitem->setParent($parent);
            if (stripos($request->attributes->get('_route'), $menuitem->getRoute()) === 0) {
                $menuitem->setActive(true);
            }
            $children[] = $menuitem;
        } else if ('KunstmaanAdminBundle_settings_users' == $parent->getRoute()) {
            $menuitem = new MenuItem($menu);
            $menuitem->setRoute('KunstmaanAdminBundle_settings_users_add');
            $menuitem->setInternalname('Add user');
            $menuitem->setParent($parent);
            $menuitem->setAppearInNavigation(false);
            if (stripos($request->attributes->get('_route'), $menuitem->getRoute()) === 0) {
                $menuitem->setActive(true);
            }
            $children[] = $menuitem;

            $menuitem = new MenuItem($menu);
            $menuitem->setRoute('KunstmaanAdminBundle_settings_users_edit');
            $menuitem->setInternalname('Edit user');
            $menuitem->setParent($parent);
            $menuitem->setAppearInNavigation(false);
            if (stripos($request->attributes->get('_route'), $menuitem->getRoute()) === 0) {
                $menuitem->setActive(true);
            }
            $children[] = $menuitem;
        } else if ('KunstmaanAdminBundle_settings_groups' == $parent->getRoute()) {
            $menuitem = new MenuItem($menu);
            $menuitem->setRoute('KunstmaanAdminBundle_settings_groups_add');
            $menuitem->setInternalname('Add group');
            $menuitem->setParent($parent);
            $menuitem->setAppearInNavigation(false);
            if (stripos($request->attributes->get('_route'), $menuitem->getRoute()) === 0) {
                $menuitem->setActive(true);
            }
            $children[] = $menuitem;

            $menuitem = new MenuItem($menu);
            $menuitem->setRoute('KunstmaanAdminBundle_settings_groups_edit');
            $menuitem->setInternalname('Edit group');
            $menuitem->setParent($parent);
            $menuitem->setAppearInNavigation(false);
            if (stripos($request->attributes->get('_route'), $menuitem->getRoute()) === 0) {
                $menuitem->setActive(true);
            }
            $children[] = $menuitem;
        } else if ('KunstmaanAdminBundle_settings_roles' == $parent->getRoute()) {
            $menuitem = new MenuItem($menu);
            $menuitem->setRoute('KunstmaanAdminBundle_settings_roles_add');
            $menuitem->setInternalname('Add role');
            $menuitem->setParent($parent);
            $menuitem->setAppearInNavigation(false);
            if (stripos($request->attributes->get('_route'), $menuitem->getRoute()) === 0) {
                $menuitem->setActive(true);
            }
            $children[] = $menuitem;

            $menuitem = new MenuItem($menu);
            $menuitem->setRoute('KunstmaanAdminBundle_settings_roles_edit');
            $menuitem->setInternalname('Edit role');
            $menuitem->setParent($parent);
            $menuitem->setAppearInNavigation(false);
            if (stripos($request->attributes->get('_route'), $menuitem->getRoute()) === 0) {
                $menuitem->setActive(true);
            }
            $children[] = $menuitem;
        }
    }

}

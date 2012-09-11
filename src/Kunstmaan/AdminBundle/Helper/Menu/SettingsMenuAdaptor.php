<?php

namespace Kunstmaan\AdminBundle\Helper\Menu;

use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;

use Symfony\Component\HttpFoundation\Request;

/**
 * The Settings Menu Adaptor
 */
class SettingsMenuAdaptor implements MenuAdaptorInterface
{

    /**
     * In this method you can add children for a specific parent, but also remove and change the already created children
     *
     * @param MenuBuilder $menu The MenuBuilder
     * @param MenuItem[]  &$children The current children
     * @param MenuItem $parent  The parent Menu item
     * @param Request  $request The Request
     */
    public function adaptChildren(MenuBuilder $menu, array &$children, MenuItem $parent = null, Request $request = null)
    {
        if (is_null($parent)) {
            $menuItem = new TopMenuItem($menu);
            $menuItem->setRoute('KunstmaanAdminBundle_settings');
            $menuItem->setInternalname('Settings');
            $menuItem->setParent($parent);
            $menuItem->setRole("settings");
            if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                $menuItem->setActive(true);
            }
            $children[] = $menuItem;
        } elseif ('KunstmaanAdminBundle_settings' == $parent->getRoute()) {
            $menuItem = new MenuItem($menu);
            $menuItem->setRoute('KunstmaanAdminBundle_settings_users');
            $menuItem->setInternalname('Users');
            $menuItem->setParent($parent);
            if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                $menuItem->setActive(true);
            }
            $children[] = $menuItem;

            $menuItem = new MenuItem($menu);
            $menuItem->setRoute('KunstmaanAdminBundle_settings_groups');
            $menuItem->setInternalname('Groups');
            $menuItem->setParent($parent);
            if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                $menuItem->setActive(true);
            }
            $children[] = $menuItem;

            $menuItem = new MenuItem($menu);
            $menuItem->setRoute('KunstmaanAdminBundle_settings_roles');
            $menuItem->setInternalname('Roles');
            $menuItem->setParent($parent);
            if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                $menuItem->setActive(true);
            }
            $children[] = $menuItem;

            $menuItem = new MenuItem($menu);
            $menuItem->setRoute('KunstmaanAdminBundle_settings_logs');
            $menuItem->setInternalname('Logs');
            $menuItem->setParent($parent);
            if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                $menuItem->setActive(true);
            }
            $children[] = $menuItem;
        } else {
            if ('KunstmaanAdminBundle_settings_users' == $parent->getRoute()) {
                $menuItem = new MenuItem($menu);
                $menuItem->setRoute('KunstmaanAdminBundle_settings_users_add');
                $menuItem->setInternalname('Add user');
                $menuItem->setParent($parent);
                $menuItem->setAppearInNavigation(false);
                if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                    $menuItem->setActive(true);
                }
                $children[] = $menuItem;

                $menuItem = new MenuItem($menu);
                $menuItem->setRoute('KunstmaanAdminBundle_settings_users_edit');
                $menuItem->setInternalname('Edit user');
                $menuItem->setParent($parent);
                $menuItem->setAppearInNavigation(false);
                if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                    $menuItem->setActive(true);
                }
                $children[] = $menuItem;
            } else {
                if ('KunstmaanAdminBundle_settings_groups' == $parent->getRoute()) {
                    $menuItem = new MenuItem($menu);
                    $menuItem->setRoute('KunstmaanAdminBundle_settings_groups_add');
                    $menuItem->setInternalname('Add group');
                    $menuItem->setParent($parent);
                    $menuItem->setAppearInNavigation(false);
                    if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                        $menuItem->setActive(true);
                    }
                    $children[] = $menuItem;

                    $menuItem = new MenuItem($menu);
                    $menuItem->setRoute('KunstmaanAdminBundle_settings_groups_edit');
                    $menuItem->setInternalname('Edit group');
                    $menuItem->setParent($parent);
                    $menuItem->setAppearInNavigation(false);
                    if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                        $menuItem->setActive(true);
                    }
                    $children[] = $menuItem;
                } else {
                    if ('KunstmaanAdminBundle_settings_roles' == $parent->getRoute()) {
                        $menuItem = new MenuItem($menu);
                        $menuItem->setRoute('KunstmaanAdminBundle_settings_roles_add');
                        $menuItem->setInternalname('Add role');
                        $menuItem->setParent($parent);
                        $menuItem->setAppearInNavigation(false);
                        if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                            $menuItem->setActive(true);
                        }
                        $children[] = $menuItem;

                        $menuItem = new MenuItem($menu);
                        $menuItem->setRoute('KunstmaanAdminBundle_settings_roles_edit');
                        $menuItem->setInternalname('Edit role');
                        $menuItem->setParent($parent);
                        $menuItem->setAppearInNavigation(false);
                        if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                            $menuItem->setActive(true);
                        }
                        $children[] = $menuItem;
                    }
                }
            }
        }
    }

}

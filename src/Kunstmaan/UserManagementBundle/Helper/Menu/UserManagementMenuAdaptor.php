<?php

namespace Kunstmaan\UserManagementBundle\Helper\Menu;

use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

class UserManagementMenuAdaptor implements MenuAdaptorInterface
{
    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

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
            return;
        }
        else if ('KunstmaanAdminBundle_settings' == $parent->getRoute()) {
            if ($this->securityContext->isGranted('ROLE_SUPER_ADMIN')) {
                $menuItem = new MenuItem($menu);
                $menuItem->setRoute('KunstmaanUserManagementBundle_settings_users')
                    ->setInternalName('Users')
                    ->setParent($parent);
                if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                    $menuItem->setActive(true);
                    $parent->setActive(true);
                }
                $children[] = $menuItem;

                $menuItem = new MenuItem($menu);
                $menuItem->setRoute('KunstmaanUserManagementBundle_settings_groups')
                    ->setInternalName('Groups')
                    ->setParent($parent);

                if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                    $menuItem->setActive(true);
                    $parent->setActive(true);
                }
                $children[] = $menuItem;

                $menuItem = new MenuItem($menu);
                $menuItem->setRoute('KunstmaanUserManagementBundle_settings_roles')
                    ->setInternalName('Roles')
                    ->setParent($parent);
                if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                    $menuItem->setActive(true);
                    $parent->setActive(true);
                }
                $children[] = $menuItem;
            }
        } else {
            if ('KunstmaanUserManagementBundle_settings_users' == $parent->getRoute()) {
                if ($this->securityContext->isGranted('ROLE_SUPER_ADMIN')) {
                    $menuItem = new MenuItem($menu);
                    $menuItem->setRoute('KunstmaanUserManagementBundle_settings_users_add')
                        ->setInternalName('Add user')
                        ->setParent($parent)
                        ->setAppearInNavigation(false);
                    if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                        $menuItem->setActive(true);
                    }
                    $children[] = $menuItem;

                    $menuItem = new MenuItem($menu);
                    $menuItem->setRoute('KunstmaanUserManagementBundle_settings_users_edit')
                        ->setInternalName('Edit user')
                        ->setParent($parent)
                        ->setAppearInNavigation(false);
                    if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                        $menuItem->setActive(true);
                    }
                    $children[] = $menuItem;
                }
            } else {
                if ('KunstmaanUserManagementBundle_settings_groups' == $parent->getRoute()) {
                    if ($this->securityContext->isGranted('ROLE_SUPER_ADMIN')) {
                        $menuItem = new MenuItem($menu);
                        $menuItem->setRoute('KunstmaanUserManagementBundle_settings_groups_add')
                            ->setInternalName('Add group')
                            ->setParent($parent)
                            ->setAppearInNavigation(false);
                        if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                            $menuItem->setActive(true);
                        }
                        $children[] = $menuItem;

                        $menuItem = new MenuItem($menu);
                        $menuItem->setRoute('KunstmaanUserManagementBundle_settings_groups_edit')
                            ->setInternalName('Edit group')
                            ->setParent($parent)
                            ->setAppearInNavigation(false);
                        if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                            $menuItem->setActive(true);
                        }
                        $children[] = $menuItem;
                    }
                } else {
                    if ('KunstmaanUserManagementBundle_settings_roles' == $parent->getRoute()) {
                        if ($this->securityContext->isGranted('ROLE_SUPER_ADMIN')) {
                            $menuItem = new MenuItem($menu);
                            $menuItem->setRoute('KunstmaanUserManagementBundle_settings_roles_add')
                                ->setInternalName('Add role')
                                ->setParent($parent)
                                ->setAppearInNavigation(false);
                            if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                                $menuItem->setActive(true);
                            }
                            $children[] = $menuItem;

                            $menuItem = new MenuItem($menu);
                            $menuItem->setRoute('KunstmaanUserManagementBundle_settings_roles_edit')
                                ->setInternalName('Edit role')
                                ->setParent($parent)
                                ->setAppearInNavigation(false);
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
} 
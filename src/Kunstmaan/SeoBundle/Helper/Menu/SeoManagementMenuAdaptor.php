<?php

namespace Kunstmaan\SeoBundle\Helper\Menu;

use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

class SeoManagementMenuAdaptor implements MenuAdaptorInterface
{
    /** @var  SecurityContextInterface */
    private $security;

    /**
     * @param SecurityContextInterface $security
     */
    public function __construct(SecurityContextInterface $security)
    {
        $this->security = $security;
    }

    /**
     * In this method you can add children for a specific parent, but also remove and change the already created children
     *
     * @param MenuBuilder $menu The MenuBuilder
     * @param MenuItem[] &$children The current children
     * @param MenuItem $parent The parent Menu item
     * @param Request $request The Request
     */
    public function adaptChildren(MenuBuilder $menu, array &$children, MenuItem $parent = null, Request $request = null)
    {
        if (!is_null($parent) and ('KunstmaanAdminBundle_settings' == $parent->getRoute()) and $this->security->isGranted('ROLE_SUPER_ADMIN')) {
            $menuItem = new MenuItem($menu);
            $menuItem
                ->setRoute('KunstmaanSeoBundle_settings_robots')
                ->setLabel('Robots')
                ->setUniqueId('robots_settings')
                ->setParent($parent);
            if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                $menuItem->setActive(true);
                $parent->setActive(true);
            }
            $children[] = $menuItem;
        }
    }
}
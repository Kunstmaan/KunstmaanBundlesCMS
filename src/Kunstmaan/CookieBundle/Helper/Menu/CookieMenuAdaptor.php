<?php

namespace Kunstmaan\CookieBundle\Helper\Menu;

use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CookieMenuAdaptor
 * CookieMenuAdaptor to add the cookie settings to the modules menu.
 */
class CookieMenuAdaptor implements MenuAdaptorInterface
{
    /**
     * In this method you can add children for a specific parent, but also remove and change the already created children
     *
     * @param MenuBuilder $menu      The MenuBuilder
     * @param MenuItem[]  &$children The current children
     * @param MenuItem    $parent    The parent Menu item
     * @param Request     $request   The Request
     */
    public function adaptChildren(MenuBuilder $menu, array &$children, ?MenuItem $parent = null, ?Request $request = null)
    {
        if (null !== $parent && 'KunstmaanAdminBundle_modules' === $parent->getRoute()) {
            $this->addMenuItem(
                $menu,
                $children,
                $parent,
                $request,
                'kunstmaancookiebundle_admin_cookies',
                'kunstmaancookiebundle_admin_cookies',
                'kuma.cookie.menu.cookies'
            );
        }

        if (null !== $parent && 'kunstmaancookiebundle_admin_cookies' === $parent->getRoute()) {
            $this->addMenuItem(
                $menu,
                $children,
                $parent,
                $request,
                'kunstmaancookiebundle_admin_cookietype',
                'kunstmaancookiebundle_admin_cookietype',
                'kuma.cookie.menu.cookie_types'
            );
            $this->addMenuItem(
                $menu,
                $children,
                $parent,
                $request,
                'kunstmaancookiebundle_admin_cookie',
                'kunstmaancookiebundle_admin_cookie',
                'kuma.cookie.menu.cookie'
            );
        }
    }

    private function addMenuItem(MenuBuilder $menu, array &$children, MenuItem $parent, ?Request $request, string $route, string $uniqueId, string $label)
    {
        $menuItem = new TopMenuItem($menu);
        $menuItem
            ->setRoute($route)
            ->setUniqueId($uniqueId)
            ->setLabel($label)
            ->setParent($parent);

        if (null !== $request && $request->attributes->get('_route') === $menuItem->getRoute()) {
            $menuItem->setActive(true);
            $parent->setActive(true);
        }

        $children[] = $menuItem;
    }
}

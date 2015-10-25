<?php

namespace Kunstmaan\AdminBundle\Helper\Menu;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class SimpleMenuAdaptor implements MenuAdaptorInterface
{

    /**
     * @var SecurityContext
     */
    private $securityContext;

    /**
     * @var array
     */
    private $menuItems;

    public function __construct(SecurityContext $securityContext, array $menuItems)
    {
        $this->securityContext = $securityContext;
        $this->menuItems = $menuItems;
    }

    /**
     * In this method you can add children for a specific parent, but also remove and change the already created
     * children
     *
     * @param MenuBuilder $menu The MenuBuilder
     * @param MenuItem[] &$children The current children
     * @param MenuItem|null $parent The parent Menu item
     * @param Request $request The Request
     */
    public function adaptChildren(MenuBuilder $menu, array &$children, MenuItem $parent = null, Request $request = null)
    {
        foreach ($this->menuItems as $item) {
            if (false === $this->parentMatches($parent, $item)) {
                continue;
            }

            if ($item['role'] && false === $this->securityContext->isGranted($item['role'])) {
                continue;
            }

            $menuItem = new TopMenuItem($menu);
            $menuItem
                ->setRoute($item['route'], $item['params'])
                ->setLabel($item['label'])
                ->setUniqueId($item['route'])
                ->setParent($parent);

            if ($request && stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                $menuItem->setActive(true);
                $parent->setActive(true);
            }

            $children[] = $menuItem;
        }
    }

    /**
     * @param MenuItem $parent
     * @param array    $item
     *
     * @return bool
     */
    private function parentMatches(MenuItem $parent = null, $item)
    {
        if (null === $parent) {
            return null === $item['parent'];
        }

        return $item['parent'] === $parent->getRoute();
    }
}

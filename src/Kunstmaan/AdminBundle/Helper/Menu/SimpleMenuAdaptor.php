<?php

namespace Kunstmaan\AdminBundle\Helper\Menu;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SimpleMenuAdaptor implements MenuAdaptorInterface
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var array
     */
    private $menuItems;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker, array $menuItems)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->menuItems = $menuItems;
    }

    public function adaptChildren(MenuBuilder $menu, array &$children, MenuItem $parent = null, Request $request = null)
    {
        foreach ($this->menuItems as $item) {
            if (false === $this->parentMatches($parent, $item)) {
                continue;
            }

            if ($item['role'] && false === $this->authorizationChecker->isGranted($item['role'])) {
                continue;
            }

            $menuItem = new TopMenuItem($menu);
            $menuItem
                ->setRoute($item['route'], $item['params'])
                ->setLabel($item['label'])
                ->setUniqueId($item['route'])
                ->setParent($parent);

            if ($request && null !== $parent && stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
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

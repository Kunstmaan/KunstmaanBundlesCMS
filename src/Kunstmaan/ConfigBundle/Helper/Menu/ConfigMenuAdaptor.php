<?php

namespace Kunstmaan\ConfigBundle\Helper\Menu;

use Symfony\Component\HttpFoundation\Request;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ConfigMenuAdaptor implements MenuAdaptorInterface
{
    /**
     * @var array $configuration
     */
    private $configuration;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @param array $configuration
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct($configuration, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->configuration = $configuration;
        $this->authorizationChecker = $authorizationChecker;
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
        if (!is_null($parent) && 'KunstmaanAdminBundle_settings' == $parent->getRoute()) {
            // Load all the kunstmaan_config entities and create a menu item for them.
            foreach ($this->configuration['entities'] as $class) {
                $entity = new $class;

                $hasAccess = false;
                foreach ($entity->getRoles() as $role) {
                    if ($this->authorizationChecker->isGranted($role)) {
                        $hasAccess = true;
                    }
                }

                if ($hasAccess) {
                    $menuItem = new MenuItem($menu);
                    $menuItem
                      ->setRoute('kunstmaanconfigbundle_default')
                      ->setRouteParams(array('internal_name' => $entity->getInternalName()))
                      ->setLabel($entity->getLabel())
                      ->setUniqueId($entity->getInternalName())
                      ->setParent($parent);

                    if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                        $menuItem->setActive(true);
                        $parent->setActive(true);
                    }
                    $children[] = $menuItem;
                }
            }
        }
    }
}

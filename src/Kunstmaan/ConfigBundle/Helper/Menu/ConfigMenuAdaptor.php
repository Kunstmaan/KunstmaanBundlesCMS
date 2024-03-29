<?php

namespace Kunstmaan\ConfigBundle\Helper\Menu;

use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ConfigMenuAdaptor implements MenuAdaptorInterface
{
    /**
     * @var array
     */
    private $configuration;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @param array $configuration
     */
    public function __construct($configuration, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->configuration = $configuration;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function adaptChildren(MenuBuilder $menu, array &$children, ?MenuItem $parent = null, ?Request $request = null)
    {
        if (!\is_null($parent) && 'KunstmaanAdminBundle_settings' == $parent->getRoute()) {
            // Load all the kunstmaan_config entities and create a menu item for them.
            foreach ($this->configuration['entities'] as $class) {
                $entity = new $class();

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
                      ->setRouteParams(['internalName' => $entity->getInternalName()])
                      ->setLabel($entity->getLabel())
                      ->setUniqueId($entity->getInternalName())
                      ->setParent($parent);

                    if ($request->attributes->get('_route') === $menuItem->getRoute() && $request->attributes->get('internalName') === $entity->getInternalName()) {
                        $menuItem->setActive(true);
                        $parent->setActive(true);
                    }
                    $children[] = $menuItem;
                }
            }
        }
    }
}

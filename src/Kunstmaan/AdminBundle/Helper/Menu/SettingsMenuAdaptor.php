<?php

namespace Kunstmaan\AdminBundle\Helper\Menu;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * SettingsMenuAdaptor to add the Settings MenuItem to the top menu and build the Settings tree
 */
class SettingsMenuAdaptor implements MenuAdaptorInterface
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var bool
     */
    private $isEnabledVersionChecker;

    /**
     * @param bool $isEnabledVersionChecker
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker, $isEnabledVersionChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->isEnabledVersionChecker = (bool) $isEnabledVersionChecker;
    }

    public function adaptChildren(MenuBuilder $menu, array &$children, MenuItem $parent = null, Request $request = null)
    {
        if (\is_null($parent)) {
            $menuItem = new TopMenuItem($menu);
            $menuItem
                ->setRoute('KunstmaanAdminBundle_settings')
                ->setLabel('settings.title')
                ->setUniqueId('settings')
                ->setParent($parent)
                ->setRole('settings');
            if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                $menuItem->setActive(true);
            }
            $children[] = $menuItem;
        }

        if (!\is_null($parent) && 'KunstmaanAdminBundle_settings' == $parent->getRoute()) {
            if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') && $this->isEnabledVersionChecker) {
                $menuItem = new MenuItem($menu);
                $menuItem
                    ->setRoute('KunstmaanAdminBundle_settings_bundle_version')
                    ->setLabel('settings.version.bundle')
                    ->setUniqueId('bundle_versions')
                    ->setParent($parent);
                if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                    $menuItem->setActive(true);
                }
                $children[] = $menuItem;
            }

            $menuItem = new MenuItem($menu);
            $menuItem
                ->setRoute('kunstmaanadminbundle_admin_exception')
                ->setLabel('settings.exceptions.title')
                ->setUniqueId('exceptions')
                ->setParent($parent);
            if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                $menuItem->setActive(true);
                $parent->setActive(true);
            }
            $children[] = $menuItem;
        }
    }
}

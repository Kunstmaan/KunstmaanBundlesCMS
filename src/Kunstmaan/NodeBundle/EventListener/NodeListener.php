<?php

namespace Kunstmaan\NodeBundle\EventListener;

use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\Tab;
use Kunstmaan\NodeBundle\Helper\FormWidgets\PermissionsFormWidget;
use Kunstmaan\NodeBundle\Event\AdaptFormEvent;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionAdmin;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMapInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;

class NodeListener
{
    /**
     * AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var PermissionAdmin
     */
    protected $permissionAdmin;

    /**
     * @var PermissionMapInterface
     */
    protected $permissionMap;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker The security context
     * @param PermissionAdmin               $permissionAdmin      The permission admin
     * @param PermissionMapInterface        $permissionMap        The permission map
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker, PermissionAdmin $permissionAdmin, PermissionMapInterface $permissionMap)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->permissionAdmin = $permissionAdmin;
        $this->permissionMap = $permissionMap;
    }

    /**
     * @param AdaptFormEvent $event
     */
    public function adaptForm(AdaptFormEvent $event)
    {
        if ($event->getPage() instanceof HasNodeInterface && !$event->getPage()->isStructureNode()) {
            if ($this->authorizationChecker->isGranted('ROLE_PERMISSIONMANAGER')) {
                $tabPane = $event->getTabPane();
                $tabPane->addTab(new Tab('kuma_node.tab.permissions.title', new PermissionsFormWidget($event->getPage(), $event->getNode(), $this->permissionAdmin, $this->permissionMap)));
            }
        }
    }
}

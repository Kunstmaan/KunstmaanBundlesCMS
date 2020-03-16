<?php

namespace Kunstmaan\NodeBundle\EventListener;

use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\Tab;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionAdmin;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMapInterface;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Event\AdaptFormEvent;
use Kunstmaan\NodeBundle\Helper\FormWidgets\PermissionsFormWidget;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

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
     * @var bool
     */
    private $permissionsEnabled;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker The security context
     * @param PermissionAdmin               $permissionAdmin      The permission admin
     * @param PermissionMapInterface        $permissionMap        The permission map
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker, PermissionAdmin $permissionAdmin, PermissionMapInterface $permissionMap, $permissionsEnabled = true)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->permissionAdmin = $permissionAdmin;
        $this->permissionMap = $permissionMap;
        $this->permissionsEnabled = $permissionsEnabled;
    }

    /**
     * @param AdaptFormEvent $event
     */
    public function adaptForm(AdaptFormEvent $event)
    {
        if ($this->permissionsEnabled && $event->getPage() instanceof HasNodeInterface && !$event->getPage()->isStructureNode() && $this->authorizationChecker->isGranted('ROLE_PERMISSIONMANAGER')) {
            $tabPane = $event->getTabPane();
            $tabPane->addTab(new Tab('kuma_node.tab.permissions.title', new PermissionsFormWidget($event->getPage(), $event->getNode(), $this->permissionAdmin, $this->permissionMap)));
        }
    }
}

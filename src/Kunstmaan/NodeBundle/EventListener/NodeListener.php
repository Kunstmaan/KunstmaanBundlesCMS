<?php

namespace Kunstmaan\NodeBundle\EventListener;

use Kunstmaan\NodeBundle\Helper\Tabs\PermissionTab;
use Kunstmaan\NodeBundle\Event\AdaptFormEvent;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionAdmin;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMapInterface;

use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * NodeListener
 */
class NodeListener
{

    /**
     * SecurityContextInterface
     */
    private $securityContext;

    /**
     * @var PermissionAdmin
     */
    protected $permissionAdmin;

    /**
     * @var PermissionMapInterface
     */
    protected $permissionMap;

    /**
     * @param SecurityContextInterface $securityContext The security context
     * @param PermissionAdmin          $permissionAdmin The permission admin
     * @param PermissionMapInterface   $permissionMap   The permission map
     */
    public function __construct(SecurityContextInterface $securityContext, PermissionAdmin $permissionAdmin, PermissionMapInterface $permissionMap)
    {
        $this->permissionAdmin = $permissionAdmin;
        $this->permissionMap = $permissionMap;
        $this->securityContext = $securityContext;
    }

    /**
     * @param AdaptFormEvent $event
     */
    public function adaptForm(AdaptFormEvent $event)
    {
        if ($this->securityContext->isGranted('ROLE_PERMISSIONMANAGER')) {
            $tabPane = $event->getTabPane();
            $tabPane->addTab(new PermissionTab('Permissions', $event->getPage(), $this->permissionAdmin, $this->permissionMap));
        }
    }

}

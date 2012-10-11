<?php

namespace Kunstmaan\NodeBundle\EventListener;

use Kunstmaan\NodeBundle\Tabs\PermissionTab;
use Kunstmaan\NodeBundle\Event\AdaptFormEvent;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionAdmin;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMapInterface;

use Symfony\Component\Security\Core\SecurityContextInterface;

class EditNodeListener
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
     * @param SecurityContextInterface $securityContext
     * @param PermissionAdmin          $permissionAdmin
     * @param PermissionMapInterface   $permissionMap
     */
    function __construct(SecurityContextInterface $securityContext, PermissionAdmin $permissionAdmin, PermissionMapInterface $permissionMap)
    {
        $this->permissionAdmin = $permissionAdmin;
        $this->permissionMap = $permissionMap;
        $this->securityContext = $securityContext;
    }

    /**
     * @param AdaptFormEvent $event
     */
    public function buildForm(AdaptFormEvent $event)
    {
        if ($this->securityContext->isGranted('ROLE_PERMISSIONMANAGER')) {
            $tabPane = $event->getTabPane();
            $tabPane->addTab(new PermissionTab('Permissions', $event->getNode(), $this->permissionAdmin, $this->permissionMap));
        }
    }

}

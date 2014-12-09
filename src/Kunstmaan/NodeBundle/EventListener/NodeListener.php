<?php

namespace Kunstmaan\NodeBundle\EventListener;

use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\Tab;
use Kunstmaan\NodeBundle\Helper\FormWidgets\PermissionsFormWidget;
use Kunstmaan\NodeBundle\Event\AdaptFormEvent;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionAdmin;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMapInterface;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;

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
    	if($event->getPage() instanceof HasNodeInterface) {
    		if ($this->securityContext->isGranted('ROLE_PERMISSIONMANAGER')) {
    			$tabPane = $event->getTabPane();
    			$tabPane->addTab(new Tab('Permissions', new PermissionsFormWidget($event->getPage(), $event->getNode(), $this->permissionAdmin, $this->permissionMap)));
    		}
    	}
    }

}

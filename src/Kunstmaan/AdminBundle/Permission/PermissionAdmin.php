<?php

namespace Kunstmaan\AdminBundle\Permission;

use Kunstmaan\AdminBundle\Component\Security\Acl\Permission\PermissionHelper;

use Kunstmaan\AdminBundle\Component\Security\Acl\Permission\MaskBuilder;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;
use Symfony\Component\Security\Acl\Permission\PermissionMapInterface;
use Symfony\Component\Security\Core\Role\RoleInterface;


/**
 * PermissionAdmin
 */
class PermissionAdmin
{

    protected $request       = null;
    protected $resource      = null;
    protected $em            = null;
    protected $aclProvider   = null;
    protected $permissionMap = null;
    protected $permissions   = null;

    /**
     * @param object        $resource            The object which has the permissions
     * @param EntityManager $em                  The EntityManager
     * @param array         $possiblePermissions Possible permissions
     */
    public function initialize($resource, EntityManager $em, AclProviderInterface $aclProvider, PermissionMapInterface $permissionMap)
    {
        $this->em            = $em;
        $this->resource      = $resource;
        $this->aclProvider   = $aclProvider;
        $this->permissionMap = $permissionMap;
        $this->permissions   = array();
        
        // Init permissions
        try {
            $objectIdentity = ObjectIdentity::fromDomainObject($this->resource);
            $acl = $aclProvider->findAcl($objectIdentity);
            $aces = $acl->getObjectAces();
            foreach ($aces as $ace) {
                $securityIdentity = $ace->getSecurityIdentity();
                if ($securityIdentity instanceof RoleSecurityIdentity) {
                    $this->permissions[$securityIdentity->getRole()] = new MaskBuilder($ace->getMask());
                }
            }
        } catch (AclNotFoundException $e) {
            // No Acl found - do nothing (or should we initialize with default values here?)
        }
    }

    /**
     * @return array
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @param Role $role
     *
     * @return Permission
     */
    public function getPermission($role)
    {
        if ($role instanceof RoleInterface) {
            $role = $role->getRole();
        }
        if (isset($this->permissions[$role])) {
            return $this->permissions[$role];
        }

        return null;
    }

    /**
     * @return array
     */
    public function getAllRoles()
    {
        return $this->em->getRepository('KunstmaanAdminBundle:Role')->findAll();
    }

    /**
     * @return array
     */
    public function getPossiblePermissions()
    {
        return $this->permissionMap->getPossiblePermissions();
    }

    /**
     * @param Request $request
     *
     * @return boolean
     */
    public function bindRequest($request)
    {
        $this->request = $request;

        $postPermissions = $request->request->get('permissions');
        $objectIdentity = ObjectIdentity::fromDomainObject($this->resource);
        try {
            $acl = $this->aclProvider->findAcl($objectIdentity);
        } catch (AclNotFoundException $e) {
            $acl = $this->aclProvider->createAcl($objectIdentity);
        }
        
        foreach ($postPermissions as $role => $permissions) {
            $mask = new MaskBuilder();
            foreach ($permissions as $permission => $value) {
                $mask->add($permission);
            }
            
            $index = PermissionHelper::getObjectAceIndex($acl, $role);
            if (false !== $index) {
                $acl->updateObjectAce($index, $mask->get());
            } else {
                $securityIdentity = new RoleSecurityIdentity($role);
                $acl->insertObjectAce($securityIdentity, $mask->get());
            }
        }
        $this->aclProvider->updateAcl($acl);
        
        return true;
    }
}

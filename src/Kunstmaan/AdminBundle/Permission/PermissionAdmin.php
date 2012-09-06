<?php

namespace Kunstmaan\AdminBundle\Permission;

use Kunstmaan\AdminBundle\Component\Security\Acl\Permission\MaskBuilder;
use Kunstmaan\AdminNodeBundle\Entity\AclChangeset;
use Kunstmaan\AdminNodeBundle\Helper\ShellHelper;
use Kunstmaan\AdminBundle\Component\Security\Acl\Permission\PermissionMapInterface;

use Doctrine\ORM\EntityManager;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;
use Symfony\Component\Security\Core\Role\RoleInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * PermissionAdmin
 */
class PermissionAdmin
{
    const ADD       = 'ADD';
    const DELETE    = 'DEL';

    protected $resource             = null;
    protected $em                   = null;
    protected $securityContext      = null;
    protected $aclProvider          = null;
    protected $oidRetrievalStrategy = null;
    protected $shellHelper          = null;
    protected $permissionMap        = null;
    protected $permissions          = null;
    protected $kernel               = null;

    /**
     * @param EntityManager                            $em                   The EntityManager
     * @param SecurityContextInterface                 $securityContext      The security context
     * @param AclProviderInterface                     $aclProvider          The ACL provider
     * @param ObjectIdentityRetrievalStrategyInterface $oidRetrievalStrategy The object retrieval strategy
     * @param KernelInterface                          $kernel               The app kernel
     */
    public function __construct(EntityManager $em, SecurityContextInterface $securityContext, AclProviderInterface $aclProvider, ObjectIdentityRetrievalStrategyInterface $oidRetrievalStrategy, KernelInterface $kernel)
    {
        $this->em = $em;
        $this->securityContext = $securityContext;
        $this->aclProvider = $aclProvider;
        $this->oidRetrievalStrategy = $oidRetrievalStrategy;
        $this->kernel = $kernel;
    }

    /**
     * @param object                 $resource      The object which has the permissions
     * @param PermissionMapInterface $permissionMap The permission map to use
     * @param ShellHelper            $shellHelper   The shell helper class to use
     */
    public function initialize($resource, PermissionMapInterface $permissionMap, ShellHelper $shellHelper)
    {
        $this->resource = $resource;
        $this->permissionMap = $permissionMap;
        $this->shellHelper = $shellHelper;
        $this->permissions = array();

        // Init permissions
        try {
            $objectIdentity = $this->oidRetrievalStrategy->getObjectIdentity($this->resource);
            $acl = $this->aclProvider->findAcl($objectIdentity);
            $objectAces = $acl->getObjectAces();
            foreach ($objectAces as $ace) {
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
     * @return MaskBuilder[]
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @param Role|string $role
     *
     * @return MaskBuilder|null
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
    public function bindRequest(Request $request)
    {
        $changes = $request->request->get('permissionChanges');

        // Just apply the changes to the current node (non recursively)
        $this->applyAclChangeset($this->resource, $changes, false);

        // Apply recursively (on request)
        $applyRecursive = $request->request->get('applyRecursive');
        if ($applyRecursive) {
            // Serialize changes & store them in DB
            $user = $this->securityContext->getToken()->getUser();
            $aclChangeset = new AclChangeset();
            $aclChangeset->setNode($this->resource);
            $aclChangeset->setChangeset($changes);
            $aclChangeset->setUser($user);
            $this->em->persist($aclChangeset);
            $this->em->flush();

            // Launch acl command
            $cmd = 'php ' . $this->kernel->getRootDir() . '/console kuma:acl:apply';
            $cmd .= ' --env=' . $this->kernel->getEnvironment();

            $this->shellHelper->runInBackground($cmd);
        }

        return true;
    }

    public function applyAclChangeset($node, $changeset, $recursive = true)
    {
        if ($recursive) {
            // Iterate over children and apply recursively
            foreach ($node->getChildren() as $child) {
                $this->applyAclChangeset($child, $changeset);
            }
        }

        // Apply ACL modifications to node
        $objectIdentity = $this->oidRetrievalStrategy->getObjectIdentity($node);
        try {
            $acl = $this->aclProvider->findAcl($objectIdentity);
        } catch (AclNotFoundException $e) {
            $acl = $this->aclProvider->createAcl($objectIdentity);
        }

        // Process permissions in changeset
        foreach ($changeset as $role => $roleChanges) {
            $index = $this->getObjectAceIndex($acl, $role);
            $mask = 0;
            if (false !== $index) {
                $mask = $this->getMaskAtIndex($acl, $index);
            }
            foreach ($roleChanges as $type => $permissions) {
                $maskChange = new MaskBuilder();
                foreach ($permissions as $permission) {
                    $maskChange->add($permission);
                }
                switch ($type) {
                    case self::ADD:
                        $mask = $mask | $maskChange->get();
                        break;
                    case self::DELETE:
                        $mask = $mask & ~$maskChange->get();
                        break;
                }
            }
            if (false !== $index) {
                $acl->updateObjectAce($index, $mask);
            } else {
                $securityIdentity = new RoleSecurityIdentity($role);
                $acl->insertObjectAce($securityIdentity, $mask);
            }
        }
        $this->aclProvider->updateAcl($acl);
    }

    private function getObjectAceIndex($acl, $role)
    {
        $objectAces = $acl->getObjectAces();
        foreach ($objectAces as $index => $ace) {
            $securityIdentity = $ace->getSecurityIdentity();
            if ($securityIdentity instanceof RoleSecurityIdentity) {
                if ($securityIdentity->getRole() == $role) {
                    return $index;
                }
            }
        }

        return false;
    }

    private function getMaskAtIndex($acl, $index)
    {
        $objectAces = $acl->getObjectAces();
        $ace = $objectAces[$index];
        $securityIdentity = $ace->getSecurityIdentity();
        if ($securityIdentity instanceof RoleSecurityIdentity) {
            return $ace->getMask();
        }

        return false;
    }

}

<?php

namespace Kunstmaan\AdminBundle\Helper\Security\Acl\Permission;

use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder;
use Kunstmaan\AdminNodeBundle\Entity\AclChangeset;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;
use Symfony\Component\Security\Acl\Permission\PermissionMapInterface;
use Symfony\Component\Security\Core\Role\RoleInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * PermissionAdmin
 */
class PermissionAdmin
{

    protected $request              = null;
    protected $resource             = null;
    protected $em                   = null;
    protected $securityContext      = null;
    protected $aclProvider          = null;
    protected $oidRetrievalStrategy = null;
    protected $shellHelper          = null;
    protected $permissionMap        = null;
    protected $permissions          = null;
    protected $currentEnv           = 'dev';

    /**
     * @param EntityManager                            $em                   The EntityManager
     * @param SecurityContextInterface                 $securityContext      The security context
     * @param AclProviderInterface                     $aclProvider          The ACL provider
     * @param ObjectIdentityRetrievalStrategyInterface $oidRetrievalStrategy The object retrieval strategy
     * @param string                                   $currentEnv           The current environment
     */
    public function __construct(EntityManager $em, SecurityContextInterface $securityContext, AclProviderInterface $aclProvider, ObjectIdentityRetrievalStrategyInterface $oidRetrievalStrategy, $currentEnv)
    {
        $this->em = $em;
        $this->securityContext = $securityContext;
        $this->aclProvider = $aclProvider;
        $this->oidRetrievalStrategy = $oidRetrievalStrategy;
        $this->currentEnv = $currentEnv;
    }

    /**
     * @param object                 $resource      The object which has the permissions
     * @param PermissionMapInterface $permissionMap The permission map to use
     * @param ShellHelper            $shellHelper   The shell helper class to use
     */
    public function initialize($resource, PermissionMapInterface $permissionMap, $shellHelper)
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
     * @param Role $role
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
    public function bindRequest($request)
    {
        $this->request = $request;

        $postPermissions = $request->request->get('permissions');
        $acl = $this->processAclChanges($postPermissions);
        $this->aclProvider->updateAcl($acl);

        // Apply recursively (on request)
        $applyRecursive = $request->request->get('applyRecursive');
        if ($applyRecursive) {
            // Serialize changes & store them in DB
            $changes = $request->request->get('permissionChanges');
            $user = $this->securityContext->getToken()->getUser();
            $this->createAclChangeSet($this->resource, $changes, $user);
            $this->launchAclChangeSet();
        }

        return true;
    }

    public function processAclChanges($postPermissions)
    {
        $objectIdentity = $this->oidRetrievalStrategy->getObjectIdentity($this->resource);
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

            $index = $this->getObjectAceIndex($acl, $role);
            if (false !== $index) {
                $acl->updateObjectAce($index, $mask->get());
            } else {
                $securityIdentity = new RoleSecurityIdentity($role);
                $acl->insertObjectAce($securityIdentity, $mask->get());
            }
        }

        // Process removed Aces
        foreach ($this->permissions as $role => $permission) {
            if (!isset($postPermissions[$role])) {
                $index = $this->getObjectAceIndex($acl, $role);
                if (false !== $index) {
                    $acl->updateObjectAce($index, 0);
                }
            }
        }

        return $acl;
    }

    public function createAclChangeSet($node, $changes, $user)
    {
        $aclChangeset = new AclChangeset();
        $aclChangeset->setNode($node);
        $aclChangeset->setChangeset($changes);
        $aclChangeset->setUser($user);
        $this->em->persist($aclChangeset);
        $this->em->flush();
    }

    public function launchAclChangeSet()
    {
        // Launch acl command
        $cmd = '/home/projects/clubbrugge/data/current/app/console kuma:acl:apply';
        if (!empty($this->currentEnv)) {
            $cmd .= ' --env=' . $this->currentEnv;
        }
        $this->shellHelper->runInBackground($cmd);
    }

    public function applyAclChangeset($node, $changeset)
    {
        // Iterate over children and apply recursively
        foreach ($node->getChildren() as $child) {
            $this->applyAclChangeset($child, $changeset);
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
                $mask = $this->getObjectAce($acl, $role);
            }
            foreach ($roleChanges as $type => $permissions) {
                $maskChange = new MaskBuilder();
                foreach ($permissions as $permission) {
                    $maskChange->add($permission);
                }
                switch ($type) {
                    case 'ADD':
                        $mask = $mask | $maskChange->get();
                        break;
                    case 'DEL':
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

    private function getObjectAce($acl, $role)
    {
        $objectAces = $acl->getObjectAces();
        foreach ($objectAces as $index => $ace) {
            $securityIdentity = $ace->getSecurityIdentity();
            if ($securityIdentity instanceof RoleSecurityIdentity) {
                if ($securityIdentity->getRole() == $role) {
                    return $ace->getMask();
                }
            }
        }

        return false;
    }

}

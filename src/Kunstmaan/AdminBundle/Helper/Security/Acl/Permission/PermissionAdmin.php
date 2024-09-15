<?php

namespace Kunstmaan\AdminBundle\Helper\Security\Acl\Permission;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\AdminBundle\Entity\AclChangeset;
use Kunstmaan\AdminBundle\Entity\BaseUser;
use Kunstmaan\AdminBundle\Entity\Role;
use Kunstmaan\AdminBundle\Entity\UserInterface;
use Kunstmaan\UtilitiesBundle\Helper\Shell\Shell;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Model\AclInterface;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;
use Symfony\Component\Security\Acl\Model\AuditableEntryInterface;
use Symfony\Component\Security\Acl\Model\MutableAclInterface;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Helper to manage the permissions on a certain entity
 */
class PermissionAdmin
{
    const ADD = 'ADD';
    const DELETE = 'DEL';

    /**
     * @var AbstractEntity
     */
    protected $resource;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var MutableAclProviderInterface
     */
    protected $aclProvider;

    /**
     * @var ObjectIdentityRetrievalStrategyInterface
     */
    protected $oidRetrievalStrategy;

    /**
     * @var PermissionMap
     */
    protected $permissionMap;

    /**
     * @var array
     */
    protected $permissions;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @var Shell
     */
    protected $shellHelper;

    /**
     * @param EntityManagerInterface                   $em                   The EntityManager
     * @param TokenStorageInterface                    $tokenStorage         The token storage
     * @param AclProviderInterface                     $aclProvider          The ACL provider
     * @param ObjectIdentityRetrievalStrategyInterface $oidRetrievalStrategy The object retrieval strategy
     * @param EventDispatcherInterface                 $eventDispatcher      The event dispatcher
     * @param Shell                                    $shellHelper          The shell helper
     * @param KernelInterface                          $kernel               The kernel
     */
    public function __construct(
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage,
        AclProviderInterface $aclProvider,
        ObjectIdentityRetrievalStrategyInterface $oidRetrievalStrategy,
        EventDispatcherInterface $eventDispatcher,
        Shell $shellHelper,
        KernelInterface $kernel,
    ) {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->aclProvider = $aclProvider;
        $this->oidRetrievalStrategy = $oidRetrievalStrategy;
        $this->eventDispatcher = $eventDispatcher;
        $this->shellHelper = $shellHelper;
        $this->kernel = $kernel;
    }

    /**
     * Initialize permission admin with specified entity.
     *
     * @param AbstractEntity         $resource      The object which has the permissions
     * @param PermissionMapInterface $permissionMap The permission map to use
     */
    public function initialize(AbstractEntity $resource, PermissionMapInterface $permissionMap)
    {
        $this->resource = $resource;
        $this->permissionMap = $permissionMap;
        $this->permissions = [];

        // Init permissions
        try {
            $objectIdentity = $this->oidRetrievalStrategy->getObjectIdentity($this->resource);
            /* @var $acl AclInterface */
            $acl = $this->aclProvider->findAcl($objectIdentity);
            $objectAces = $acl->getObjectAces();
            /* @var $ace AuditableEntryInterface */
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
     * Get permissions.
     *
     * @return MaskBuilder[]
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Get permission for specified role.
     *
     * @param \Symfony\Component\Security\Core\Role\Role|string $role
     *
     * @return MaskBuilder|null
     */
    public function getPermission($role)
    {
        // NEXT_MAJOR remove undefined classes from this check
        if ($role instanceof \Symfony\Component\Security\Core\Role\Role || $role instanceof Role) {
            $role = $role->getRole();
        }

        if (isset($this->permissions[$role])) {
            return $this->permissions[$role];
        }

        return null;
    }

    /**
     * Get all roles.
     *
     * @return Role[]
     */
    public function getAllRoles()
    {
        return $this->em->getRepository(Role::class)->findAll();
    }

    /**
     * Get all manageable roles for pages
     *
     * @return Role[]
     */
    public function getManageableRolesForPages()
    {
        $roles = $this->em->getRepository(Role::class)->findAll();

        if (($token = $this->tokenStorage->getToken()) && ($user = $token->getUser())) {
            if ($user && !$user->isSuperAdmin() && ($superAdminRole = array_keys($roles, 'ROLE_SUPER_ADMIN'))) {
                $superAdminRole = current($superAdminRole);
                unset($roles[$superAdminRole]);
            }
        }

        return $roles;
    }

    /**
     * Get possible permissions.
     *
     * @return array
     */
    public function getPossiblePermissions()
    {
        return $this->permissionMap->getPossiblePermissions();
    }

    /**
     * Handle form entry of permission changes.
     *
     * @return bool
     */
    public function bindRequest(Request $request)
    {
        $changes = $request->request->all('permission-hidden-fields');

        if (empty($changes)) {
            return true;
        }

        // Just apply the changes to the current node (non recursively)
        $this->applyAclChangeset($this->resource, $changes, false);

        // Apply recursively (on request)
        $applyRecursive = $request->request->get('applyRecursive');
        if ($applyRecursive) {
            // Serialize changes & store them in DB
            $user = $this->tokenStorage->getToken()->getUser();
            $this->createAclChangeSet($this->resource, $changes, $user);

            $cmd = 'php ' . $this->kernel->getProjectDir() . '/bin/console kuma:acl:apply';
            $cmd .= ' --env=' . $this->kernel->getEnvironment();

            $this->shellHelper->runInBackground($cmd);
        }

        return true;
    }

    /**
     * Create a new ACL changeset.
     *
     * @param AbstractEntity $entity  The entity
     * @param array          $changes The changes
     * @param UserInterface  $user    The user
     *
     * @return AclChangeset
     */
    public function createAclChangeSet(AbstractEntity $entity, $changes, UserInterface $user)
    {
        $aclChangeset = new AclChangeset();
        $aclChangeset->setRef($entity);
        $aclChangeset->setChangeset($changes);
        /* @var $user BaseUser */
        $aclChangeset->setUser($user);
        $this->em->persist($aclChangeset);
        $this->em->flush();

        return $aclChangeset;
    }

    /**
     * Apply the specified ACL changeset.
     *
     * @param AbstractEntity $entity    The entity
     * @param array          $changeset The changeset
     * @param bool           $recursive The recursive
     */
    public function applyAclChangeset(AbstractEntity $entity, $changeset, $recursive = true)
    {
        if ($recursive) {
            if (!method_exists($entity, 'getChildren')) {
                return;
            }

            // Iterate over children and apply recursively
            foreach ($entity->getChildren() as $child) {
                $this->applyAclChangeset($child, $changeset);
            }
        }

        // Apply ACL modifications to node
        $objectIdentity = $this->oidRetrievalStrategy->getObjectIdentity($entity);

        try {
            /* @var $acl MutableAclInterface */
            $acl = $this->aclProvider->findAcl($objectIdentity);
        } catch (AclNotFoundException $e) {
            /* @var $acl MutableAclInterface */
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
                        $mask |= $maskChange->get();

                        break;
                    case self::DELETE:
                        $mask &= ~$maskChange->get();

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

    /**
     * Get current object ACE index for specified role.
     *
     * @param AclInterface $acl  The AclInterface
     * @param string       $role The role
     */
    private function getObjectAceIndex(AclInterface $acl, $role): bool|int
    {
        $objectAces = $acl->getObjectAces();
        /* @var $ace AuditableEntryInterface */
        foreach ($objectAces as $index => $ace) {
            $securityIdentity = $ace->getSecurityIdentity();
            if (($securityIdentity instanceof RoleSecurityIdentity) && $securityIdentity->getRole() == $role) {
                return $index;
            }
        }

        return false;
    }

    /**
     * Get object ACE mask at specified index.
     *
     * @param AclInterface $acl   The acl interface
     * @param int          $index The index
     */
    private function getMaskAtIndex(AclInterface $acl, $index): bool|int
    {
        $objectAces = $acl->getObjectAces();
        /* @var $ace AuditableEntryInterface */
        $ace = $objectAces[$index];
        $securityIdentity = $ace->getSecurityIdentity();
        if ($securityIdentity instanceof RoleSecurityIdentity) {
            return $ace->getMask();
        }

        return false;
    }
}

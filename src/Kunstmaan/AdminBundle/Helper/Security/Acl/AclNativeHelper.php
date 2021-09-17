<?php

namespace Kunstmaan\AdminBundle\Helper\Security\Acl;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionDefinition;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

/**
 * AclHelper is a helper class to help setting the permissions when querying using native queries
 *
 * @see https://gist.github.com/1363377
 */
class AclNativeHelper
{
    /**
     * @var EntityManager
     */
    private $em = null;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage = null;

    /**
     * @var RoleHierarchyInterface
     */
    private $roleHierarchy = null;

    /**
     * @var bool
     */
    private $permissionsEnabled;

    /**
     * @param EntityManager          $em           The entity manager
     * @param TokenStorageInterface  $tokenStorage The security context
     * @param RoleHierarchyInterface $rh           The role hierarchies
     */
    public function __construct(EntityManager $em, TokenStorageInterface $tokenStorage, RoleHierarchyInterface $rh, $permissionsEnabled = true)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->roleHierarchy = $rh;
        $this->permissionsEnabled = $permissionsEnabled;
    }

    /**
     * Apply the ACL constraints to the specified query builder, using the permission definition, for all database platforms.
     *
     * @return QueryBuilder
     */
    public function apply(QueryBuilder $queryBuilder, PermissionDefinition $permissionDef)
    {
        if (!$this->permissionsEnabled) {
            return $queryBuilder;
        }

        $databasePlatform = $this->em->getConnection()->getDatabasePlatform();
        $rootEntity = $permissionDef->getEntity();
        $linkAlias = $permissionDef->getAlias();
        // Only tables with a single ID PK are currently supported
        $linkField = $this->em->getClassMetadata($rootEntity)->getSingleIdentifierColumnName();

        $rootEntity = $databasePlatform->quoteStringLiteral($rootEntity);
        $query = $queryBuilder;

        $builder = new MaskBuilder();
        foreach ($permissionDef->getPermissions() as $permission) {
            $mask = \constant(\get_class($builder) . '::MASK_' . strtoupper($permission));
            $builder->add($mask);
        }
        $mask = $builder->get();

        /* @var $token TokenInterface */
        $token = $this->tokenStorage->getToken();
        $userRoles = [];
        $user = null;
        if (!\is_null($token)) {
            $user = $token->getUser();
            if (method_exists($this->roleHierarchy, 'getReachableRoleNames')) {
                $userRoles = $this->roleHierarchy->getReachableRoleNames($token->getRoleNames());
            } else {
                // Symfony 3.4 compatibility
                $userRoles = $this->roleHierarchy->getReachableRoles($token->getRoles());
            }
        }

        // Security context does not provide anonymous role automatically.
        $uR = [$databasePlatform->quoteStringLiteral('IS_AUTHENTICATED_ANONYMOUSLY')];

        foreach ($userRoles as $role) {
            // The reason we ignore this is because by default FOSUserBundle adds ROLE_USER for every user
            if (is_string($role)) {
                if ($role !== 'ROLE_USER') {
                    $uR[] = $databasePlatform->quoteStringLiteral($role);
                }
            } else {
                // Symfony 3.4 compatibility
                if ($role->getRole() !== 'ROLE_USER') {
                    $uR[] = $databasePlatform->quoteStringLiteral($role->getRole());
                }
            }
        }
        $uR = array_unique($uR);
        $inString = implode(' OR s.identifier = ', $uR);

        if (\is_object($user)) {
            $inString .= ' OR s.identifier = ' . $databasePlatform->quoteStringLiteral(\get_class($user) . '-' . $user->getUserName());
        }

        $objectIdentifierColumn = 'o.object_identifier';
        if ($databasePlatform->getName() === 'postgresql') {
            $objectIdentifierColumn .= '::BIGINT';
        }

        $joinTableQuery = <<<SELECTQUERY
SELECT DISTINCT {$objectIdentifierColumn} as id FROM acl_object_identities as o
INNER JOIN acl_classes c ON c.id = o.class_id
LEFT JOIN acl_entries e ON (
    e.class_id = o.class_id AND (e.object_identity_id = o.id
    OR {$databasePlatform->getIsNullExpression('e.object_identity_id')})
)
LEFT JOIN acl_security_identities s ON (
s.id = e.security_identity_id
)
WHERE c.class_type = {$rootEntity}
AND (s.identifier = {$inString})
AND e.mask & {$mask} > 0
SELECTQUERY;

        $query->join($linkAlias, '(' . $joinTableQuery . ')', 'perms_', 'perms_.id = ' . $linkAlias . '.' . $linkField);

        return $query;
    }

    /**
     * @return TokenStorageInterface|null
     */
    public function getTokenStorage()
    {
        return $this->tokenStorage;
    }
}

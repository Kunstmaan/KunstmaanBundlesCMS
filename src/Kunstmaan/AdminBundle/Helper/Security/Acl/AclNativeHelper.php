<?php

namespace Kunstmaan\AdminBundle\Helper\Security\Acl;

use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionDefinition;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManager;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Role\RoleInterface;
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
     * @var SecurityContextInterface
     */
    private $securityContext = null;

    /**
     * @var RoleHierarchyInterface
     */
    private $roleHierarchy = null;

    /**
     * Constructor.
     *
     * @param EntityManager            $em The entity manager
     * @param SecurityContextInterface $sc The security context
     * @param RoleHierarchyInterface   $rh The role hierarchies
     */
    public function __construct(EntityManager $em, SecurityContextInterface $sc, RoleHierarchyInterface $rh)
    {
        $this->em              = $em;
        $this->securityContext = $sc;
        $this->roleHierarchy   = $rh;
    }

    /**
     * Apply the ACL constraints to the specified query builder, using the permission definition
     *
     * @param QueryBuilder         $queryBuilder  The query builder
     * @param PermissionDefinition $permissionDef The permission definition
     *
     * @return QueryBuilder
     */
    public function apply(QueryBuilder $queryBuilder, PermissionDefinition $permissionDef)
    {
        $aclConnection = $this->em->getConnection();

        $database   = $aclConnection->getDatabase();
        $rootEntity = $permissionDef->getEntity();
        $linkAlias = $permissionDef->getAlias();
        // Only tables with a single ID PK are currently supported
        $linkField = $this->em->getClassMetadata($rootEntity)->getSingleIdentifierColumnName();

        $rootEntity = '"' . str_replace('\\', '\\\\', $rootEntity) . '"';
        $query = $queryBuilder;

        $builder = new MaskBuilder();
        foreach ($permissionDef->getPermissions() as $permission) {
            $mask = constant(get_class($builder) . '::MASK_' . strtoupper($permission));
            $builder->add($mask);
        }
        $mask = $builder->get();

        /* @var $token TokenInterface */
        $token = $this->securityContext->getToken(); // for now lets imagine we will have token i.e user is logged in
        $user  = $token->getUser();

        $userRoles = $this->roleHierarchy->getReachableRoles($token->getRoles());

        // Security context does not provide anonymous role automatically.
        $uR = array('"IS_AUTHENTICATED_ANONYMOUSLY"');

        /* @var $role RoleInterface */
        foreach ($userRoles as $role) {
            // The reason we ignore this is because by default FOSUserBundle adds ROLE_USER for every user
            if ($role->getRole() !== 'ROLE_USER') {
                $uR[] = '"' . $role->getRole() . '"';
            }
        }
        $inString = implode(' OR s.identifier = ', (array) $uR);

        if (is_object($user)) {
            $inString .= ' OR s.identifier = "' . str_replace(
                '\\',
                '\\\\',
                get_class($user)
            ) . '-' . $user->getUserName() . '"';
        }

        $joinTableQuery = <<<SELECTQUERY
SELECT DISTINCT o.object_identifier as id FROM {$database}.acl_object_identities as o
INNER JOIN {$database}.acl_classes c ON c.id = o.class_id
LEFT JOIN {$database}.acl_entries e ON (
    e.class_id = o.class_id AND (e.object_identity_id = o.id
    OR {$aclConnection->getDatabasePlatform()->getIsNullExpression('e.object_identity_id')})
)
LEFT JOIN {$database}.acl_security_identities s ON (
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
     * @return null|SecurityContextInterface
     */
    public function getSecurityContext()
    {
        return $this->securityContext;
    }
}

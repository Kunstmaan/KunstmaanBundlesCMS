<?php

namespace Kunstmaan\AdminBundle\Helper\Security\Acl;

use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionDefinition;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManager;

use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * AclNativeHelper
 *
 * Based on https://gist.github.com/1363377
 */
class AclNativeHelper
{
    private $em = null;
    private $securityContext = null;

    /**
     * @param EntityManager            $em              The entity manager
     * @param SecurityContextInterface $securityContext The security context
     */
    public function __construct(EntityManager $em, SecurityContextInterface $securityContext)
    {
        $this->em              = $em;
        $this->securityContext = $securityContext;
    }

    /**
     * This will clone the original query and apply the ACL constraints
     *
     * @param QueryBuilder         $queryBuilder  The query builder
     * @param PermissionDefinition $permissionDef The permission definition
     *
     * @return type
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

        $token = $this->securityContext->getToken(); // for now lets imagine we will have token i.e user is logged in
        $user  = $token->getUser();

        if (is_object($user)) {
            $userRoles = $user->getRoles();
            foreach ($userRoles as $role) {
                // The reason we ignore this is because by default FOSUserBundle adds ROLE_USER for every user
                if ($role !== 'ROLE_USER') {
                    $uR[] = '"' . $role . '"';
                }
            }
            $inString = implode(' OR s.identifier = ', (array) $uR);
            $inString .= ' OR s.identifier = "' . str_replace(
                '\\',
                '\\\\',
                get_class($user)
            ) . '-' . $user->getUserName() . '"';
        } else {
            $userRoles = $token->getRoles();
            foreach ($userRoles as $role) {
                $role = $role->getRole();
                if ($role !== 'ROLE_USER') {
                    $uR[] = '"' . $role . '"';
                }
            }
            $inString = implode(' OR s.identifier = ', (array) $uR);
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

}

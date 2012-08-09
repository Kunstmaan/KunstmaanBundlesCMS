<?php

namespace Kunstmaan\AdminBundle\Util\Helper;

use Kunstmaan\AdminBundle\Component\Security\Acl\Permission\MaskBuilder;

use Doctrine\DBAL\Query\QueryBuilder;

/**
 * AclHelper
 *
 * Based on https://gist.github.com/1363377
 */
class AclNativeHelper
{
    
    function __construct($em, $securityContext)
    {
        $this->em = $em;
        $this->securityContext = $securityContext;
        $this->aclConnection = $em->getConnection();
    }

    protected function cloneQuery(QueryBuilder $query)
    {
        $aclAppliedQuery = clone $query;
        $params = $query->getParameters();
        foreach ($params as $key => $param) {
            $aclAppliedQuery->setParameter($key, $param);
        }

        return $aclAppliedQuery;
    }

    /**
     * This will clone the original query and apply the ACL constraints
     *
     * @param QueryBuilder $queryBuilder
     * @param array $permissions
     *
     * @return type
     */
    public function apply(QueryBuilder $queryBuilder, $linkTable, $linkField, array $permissions = array("VIEW"), $rootEntity = 'Kunstmaan\AdminNodeBundle\Entity\Node')
    {
        $database = $this->aclConnection->getDatabase();
        $rootEntity = '"' . str_replace('\\', '\\\\', $rootEntity) . '"';

        $query = $this->cloneQuery($queryBuilder);

        $builder = new MaskBuilder();
        foreach ($permissions as $permission) {
            $mask = constant(get_class($builder) . '::MASK_' . strtoupper($permission));
            $builder->add($mask);
        }
        $mask = $builder->get();

        $token = $this->securityContext->getToken(); // for now lets imagine we will have token i.e user is logged in
        $user = $token->getUser();
        $INString = "''";

        if (is_object($user)) {
            $userRoles = $user->getRoles();
            foreach ($userRoles as $role) {
                // The reason we ignore this is because by default FOSUserBundle adds ROLE_USER for every user
                if ($role !== 'ROLE_USER') {
                    $uR[] = '"' . $role . '"';
                }
            }
            $INString = implode(' OR s.identifier = ', (array) $uR);
            $INString .= ' OR s.identifier = "' . str_replace('\\', '\\\\', get_class($user)) . '-' . $user->getUserName() . '"';
        } else {
            $userRoles = $token->getRoles();
            foreach ($userRoles as $role) {
                $role = $role->getRole();
                if ($role !== 'ROLE_USER') {
                    $uR[] = '"' . $role . '"';
                }
            }
            $INString = implode(' OR s.identifier = ', (array) $uR);
        }

        $joinTableQuery = <<<SELECTQUERY
SELECT DISTINCT o.object_identifier as id FROM {$database}.acl_object_identities as o
INNER JOIN {$database}.acl_classes c ON c.id = o.class_id
LEFT JOIN {$database}.acl_entries e ON (
e.class_id = o.class_id AND (e.object_identity_id = o.id OR {$this->aclConnection->getDatabasePlatform()->getIsNullExpression('e.object_identity_id')})
)
LEFT JOIN {$database}.acl_security_identities s ON (
s.id = e.security_identity_id
)
WHERE c.class_type = {$rootEntity}
AND (s.identifier = {$INString})
AND e.mask & {$mask} > 0
SELECTQUERY;

        $query->join($linkTable, '(' . $joinTableQuery . ')', 'perms_', 'perms_.id = ' . $linkTable . '.' . $linkField);

        return $query;
    }

}

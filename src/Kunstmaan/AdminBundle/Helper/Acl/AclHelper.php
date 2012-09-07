<?php

namespace Kunstmaan\AdminBundle\Helper\Acl;

use Kunstmaan\AdminBundle\Component\Security\Acl\Permission\MaskBuilder;
use Kunstmaan\AdminBundle\Component\Security\Acl\Permission\PermissionDefinition;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;

use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * AclHelper
 *
 * Based on https://gist.github.com/1363377
 */
class AclHelper
{
    private $em = null;
    private $securityContext = null;

    /**
     * @param EntityManager            $em
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(EntityManager $em, SecurityContextInterface $securityContext)
    {
        $this->em              = $em;
        $this->securityContext = $securityContext;
    }

    /**
     * @param Query $query
     *
     * @return Query
     */
    protected function cloneQuery(Query $query)
    {
        $aclAppliedQuery = clone $query;
        $params          = $query->getParameters();
        foreach ($params as $key => $param) {
            $aclAppliedQuery->setParameter($key, $param);
        }

        return $aclAppliedQuery;
    }

    /**
     * This will clone the original query and apply the ACL constraints
     *
     * @param QueryBuilder          $queryBuilder
     * @param PermissionDefinition  $permissionDef
     *
     * @return Query
     */
    public function apply(QueryBuilder $queryBuilder, PermissionDefinition $permissionDef)
    {
        $whereQueryParts = $queryBuilder->getDQLPart('where');
        if (empty($whereQueryParts)) {
            $queryBuilder->where(
                '1 = 1'
            ); // this will help in cases where no where query is specified, where query is required to walk in where clause
        }

        $query = $this->cloneQuery($queryBuilder->getQuery());

        $builder = new MaskBuilder();
        foreach ($permissionDef->getPermissions() as $permission) {
            $mask = constant(get_class($builder) . '::MASK_' . strtoupper($permission));
            $builder->add($mask);
        }
        $query->setHint('acl.mask', $builder->get());
        $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'Kunstmaan\AdminBundle\Helper\Acl\AclWalker');

        $rootEntity = $permissionDef->getEntity();
        $rootAlias  = $permissionDef->getAlias();
        // If either alias or entity was not specified - use default from QueryBuilder
        if (empty($rootEntity) || empty($rootAlias)) {
            $rootEntities = $queryBuilder->getRootEntities();
            $rootAliases  = $queryBuilder->getRootAliases();
            $rootEntity   = $rootEntities[0];
            $rootAlias    = $rootAliases[0];
        }

        $query->setHint('acl.root.entity', $rootEntity);
        $query->setHint('acl.extra.query', $this->getPermittedAclIdsSQLForUser($query));

        $class               = $this->em->getClassMetadata($rootEntity);
        $entityRootTableName = $class->getQuotedTableName($this->em->getConnection()->getDatabasePlatform());

        $query->setHint('acl.entityRootTableName', $entityRootTableName);
        $query->setHint('acl.entityRootTableDqlAlias', $rootAlias);

        return $query;
    }

    /**
     * This query works well with small offset, but if want to use it with large offsets please refer to the link on how to implement
     * http://www.scribd.com/doc/14683263/Efficient-Pagination-Using-MySQL
     * This will only check permissions on the first entity added in the from clause, it will not check permissions
     * By default the number of rows returned are 10 starting from 0
     *
     * @param Query $query
     *
     * @return string
     */
    private function getPermittedAclIdsSQLForUser(Query $query)
    {
        $aclConnection = $this->em->getConnection();
        $database      = $aclConnection->getDatabase();
        $mask          = $query->getHint('acl.mask');
        $rootEntity    = '"' . str_replace('\\', '\\\\', $query->getHint('acl.root.entity')) . '"';

        $token = $this->securityContext->getToken(); // for now lets imagine we will have token i.e user is logged in
        $user  = $token->getUser();

        if (is_object($user)) {
            $userRoles = $user->getRoles();
            $uR        = array();
            foreach ($userRoles as $role) {
                // The reason we ignore this is because by default FOSUserBundle adds ROLE_USER for every user
                if ($role !== 'ROLE_USER') {
                    $uR[] = '"' . $role . '"';
                }
            }
            $INString = implode(' OR s.identifier = ', $uR);
            $INString .= ' OR s.identifier = "' . str_replace(
                '\\',
                '\\\\',
                get_class($user)
            ) . '-' . $user->getUserName() . '"';
        } else {
            $userRoles = $token->getRoles();
            $uR        = array();
            foreach ($userRoles as $role) {
                $role = $role->getRole();
                if ($role !== 'ROLE_USER') {
                    $uR[] = '"' . $role . '"';
                }
            }
            $INString = implode(' OR s.identifier = ', $uR);
        }

        $selectQuery = <<<SELECTQUERY
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
AND (s.identifier = {$INString})
AND e.mask & {$mask} > 0
SELECTQUERY;

        return $selectQuery;
    }

}

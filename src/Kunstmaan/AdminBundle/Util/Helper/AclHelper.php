<?php
namespace Kunstmaan\AdminBundle\Util\Helper;

use Kunstmaan\AdminBundle\Component\Security\Acl\Permission\MaskBuilder;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * AclHelper
 *
 * Based on https://gist.github.com/1363377
 */
class AclHelper
{
    
    function __construct($em, $securityContext)
    {
        $this->em = $em;
        $this->securityContext = $securityContext;
        $this->aclConnection = $em->getConnection();
    }

    protected function cloneQuery(Query $query)
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
    public function apply(QueryBuilder $queryBuilder, array $permissions = array("VIEW"))
    {

        $whereQueryParts = $queryBuilder->getDQLPart('where');
        if (empty($whereQueryParts)) {
            $fromQueryParts = $queryBuilder->getDQLPart('from');
            $firstFromQueryAlias = $fromQueryParts[0]->getAlias();
            $queryBuilder->where('1 = 1'); // this will help in cases where no where query is specified, where query is required to walk in where clause
        }

        $query = $this->cloneQuery($queryBuilder->getQuery());

        $builder = new MaskBuilder();
        foreach ($permissions as $permission) {
            $mask = constant(get_class($builder) . '::MASK_' . strtoupper($permission));
            $builder->add($mask);
        }
        $query->setHint('acl.mask', $builder->get());

        $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'Kunstmaan\AdminBundle\Util\Doctrine\SqlWalker\AclWalker');
        $entities = $queryBuilder->getRootEntities();
        $query->setHint('acl.root.entities', $entities);

        $query->setHint('acl.extra.query', $this->getPermittedAclIdsSQLForUser($query, $queryBuilder));

        $class = $this->em->getClassMetadata($entities[0]);
        $entityRootTableName = $class->getQuotedTableName($this->em->getConnection()->getDatabasePlatform());
        $entityRootAlias = $queryBuilder->getRootAlias();

        $query->setHint('acl.entityRootTableName', $entityRootTableName);
        $query->setHint('acl.entityRootTableDqlAlias', $entityRootAlias);

        return $query;
    }

    /**
     * This query works well with small offset, but if want to use it with large offsets please refer to the link on how to implement
     * http://www.scribd.com/doc/14683263/Efficient-Pagination-Using-MySQL
     * This will only check permissions on the first entity added in the from clause, it will not check permissions
     * By default the number of rows returned are 10 starting from 0
     *
     * @param Query $query
     * @param QueryBuilder $queryBuilder
     *
     * @return String Sql
     */
    private function getPermittedAclIdsSQLForUser(Query $query, QueryBuilder $queryBuilder)
    {
        $database = $this->aclConnection->getDatabase();
        $mask = $query->getHint('acl.mask');
        $rootEntities = $query->getHint('acl.root.entities');
        foreach ($rootEntities as $rootEntity) {
            $rE[] = '"' . str_replace('\\', '\\\\', $rootEntity) . '"';
            // For now ACL will be checked for first root entity, it will not check for all other entities in join etc..,
            break;
        }
        $rootEntities = implode(',', $rE);

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

        $selectQuery = <<<SELECTQUERY
SELECT DISTINCT o.object_identifier as id FROM {$database}.acl_object_identities as o
INNER JOIN {$database}.acl_classes c ON c.id = o.class_id
LEFT JOIN {$database}.acl_entries e ON (
e.class_id = o.class_id AND (e.object_identity_id = o.id OR {$this->aclConnection->getDatabasePlatform()->getIsNullExpression('e.object_identity_id')})
)
LEFT JOIN {$database}.acl_security_identities s ON (
s.id = e.security_identity_id
)
WHERE c.class_type = {$rootEntities}
AND s.identifier = {$INString}
AND e.mask & {$mask} > 0
SELECTQUERY;

        return $selectQuery;
    }
    
}

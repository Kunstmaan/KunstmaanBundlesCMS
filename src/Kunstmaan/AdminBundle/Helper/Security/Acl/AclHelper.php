<?php

namespace Kunstmaan\AdminBundle\Helper\Security\Acl;

use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionDefinition;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\QuoteStrategy;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMapping;

use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * AclHelper
 *
 * Based on https://gist.github.com/1363377
 */
class AclHelper
{
    /* @var EntityManager $em */
    private $em = null;

    /* @var SecurityContextInterface $securityContext */
    private $securityContext = null;

    /* @var QuoteStrategy $quoteStrategy */
    private $quoteStrategy = null;

    /**
     * Constructor.
     *
     * @param EntityManager            $em              The entity manager
     * @param SecurityContextInterface $securityContext The security context
     */
    public function __construct(EntityManager $em, SecurityContextInterface $securityContext)
    {
        $this->em              = $em;
        $this->securityContext = $securityContext;
        $this->quoteStrategy   = $em->getConfiguration()->getQuoteStrategy();
    }

    /**
     * Clone specified query with parameters.
     *
     * @param Query $query
     *
     * @return Query
     */
    protected function cloneQuery(Query $query)
    {
        $aclAppliedQuery = clone $query;
        $params          = $query->getParameters();
        foreach ($params as $param) {
            $aclAppliedQuery->setParameter($param->getName(), $param->getValue(), $param->getType());
        }

        return $aclAppliedQuery;
    }

    /**
     * Apply the ACL constraints to the specified query builder, using the permission definition
     *
     * @param QueryBuilder         $queryBuilder  The query builder
     * @param PermissionDefinition $permissionDef The permission definition
     *
     * @return Query
     */
    public function apply(QueryBuilder $queryBuilder, PermissionDefinition $permissionDef)
    {
        $whereQueryParts = $queryBuilder->getDQLPart('where');
        if (empty($whereQueryParts)) {
            $queryBuilder->where('1 = 1'); // this will help in cases where no where query is specified
        }

        $query = $this->cloneQuery($queryBuilder->getQuery());

        $builder = new MaskBuilder();
        foreach ($permissionDef->getPermissions() as $permission) {
            $mask = constant(get_class($builder) . '::MASK_' . strtoupper($permission));
            $builder->add($mask);
        }
        $query->setHint('acl.mask', $builder->get());
        $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'Kunstmaan\AdminBundle\Helper\Security\Acl\AclWalker');

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

        $classMeta           = $this->em->getClassMetadata($rootEntity);
        $entityRootTableName = $this->quoteStrategy->getTableName(
            $classMeta,
            $this->em->getConnection()->getDatabasePlatform()
        );
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
            $inString = implode(' OR s.identifier = ', $uR);
            $inString .= ' OR s.identifier = "' . str_replace(
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
            $inString = implode(' OR s.identifier = ', $uR);
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
AND (s.identifier = {$inString})
AND e.mask & {$mask} > 0
SELECTQUERY;

        return $selectQuery;
    }

    /**
     * Returns valid IDs for a specific entity with ACL restrictions for current user applied
     *
     * @param PermissionDefinition $permissionDef
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    public function getAllowedEntityIds(PermissionDefinition $permissionDef)
    {
        $rootEntity = $permissionDef->getEntity();
        if (empty($rootEntity)) {
            throw new \InvalidArgumentException("You have to provide an entity class name!");
        }
        $builder = new MaskBuilder();
        foreach ($permissionDef->getPermissions() as $permission) {
            $mask = constant(get_class($builder) . '::MASK_' . strtoupper($permission));
            $builder->add($mask);
        }

        $query = new Query($this->em);
        $query->setHint('acl.mask', $builder->get());
        $query->setHint('acl.root.entity', $rootEntity);
        $sql = $this->getPermittedAclIdsSQLForUser($query);

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $nativeQuery = $this->em->createNativeQuery($sql, $rsm);

        $transform = function ($item) {
            return $item['id'];
        };
        $result    = array_map($transform, $nativeQuery->getScalarResult());

        return $result;
    }

    /**
     * @return null|SecurityContextInterface
     */
    public function getSecurityContext()
    {
        return $this->securityContext;
    }
}

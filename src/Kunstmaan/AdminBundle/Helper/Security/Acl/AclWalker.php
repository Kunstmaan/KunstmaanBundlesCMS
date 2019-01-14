<?php

namespace Kunstmaan\AdminBundle\Helper\Security\Acl;

use Doctrine\ORM\Query;
use Doctrine\ORM\Query\SqlWalker;

/**
 * AclWalker
 */
class AclWalker extends SqlWalker
{
    /**
     * Walks down a FromClause AST node, thereby generating the appropriate SQL.
     *
     * @param string $fromClause
     *
     * @return string the SQL
     */
    public function walkFromClause($fromClause)
    {
        $sql = parent::walkFromClause($fromClause);
        $name = $this->getQuery()->getHint('acl.entityRootTableName');
        $alias = $this->getQuery()->getHint('acl.entityRootTableDqlAlias');
        $tableAlias = $this->getSQLTableAlias($name, $alias);
        $extraQuery = $this->getQuery()->getHint('acl.extra.query');

        $tempAclView = <<<tempAclView
JOIN ({$extraQuery}) ta_ ON {$tableAlias}.id = ta_.id
tempAclView;

        return $sql . ' ' . $tempAclView;
    }
}

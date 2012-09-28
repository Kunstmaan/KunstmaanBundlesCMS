<?php

namespace Kunstmaan\AdminListBundle\AdminList\FilterTypes\DBAL;

use Kunstmaan\AdminListBundle\AdminList\AbstractFilterType;

use Doctrine\DBAL\Query\QueryBuilder;

/**
 * the abstract filter used for DBAL query builder
 */
abstract class AbstractDBALFilterType extends AbstractFilterType
{
    /**
     * @var QueryBuilder $queryBuilder
     */
    protected $queryBuilder;

    /**
     * @param QueryBuilder $queryBuilder
     */
    public function setQueryBuilder(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
}

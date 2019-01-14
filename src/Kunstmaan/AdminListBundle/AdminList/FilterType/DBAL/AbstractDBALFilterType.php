<?php

namespace Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL;

use Doctrine\DBAL\Query\QueryBuilder;
use Kunstmaan\AdminListBundle\AdminList\FilterType\AbstractFilterType;

/**
 * the abstract filter used for DBAL query builder
 */
abstract class AbstractDBALFilterType extends AbstractFilterType
{
    /**
     * @var QueryBuilder
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

<?php

namespace Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;

use Doctrine\ORM\QueryBuilder;
use Kunstmaan\AdminListBundle\AdminList\FilterType\AbstractFilterType;

/**
 * The abstract filter used for ORM query builder
 */
abstract class AbstractORMFilterType extends AbstractFilterType
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

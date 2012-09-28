<?php

namespace Kunstmaan\AdminListBundle\AdminList\FilterTypes\ORM;

use Kunstmaan\AdminListBundle\AdminList\AbstractFilterType;

use Doctrine\ORM\QueryBuilder;

/**
 * The abstract filter used for ORM query builder
 */
abstract class AbstractORMFilterType extends AbstractFilterType
{
    /* @var QueryBuilder $queryBuilder */
    protected $queryBuilder;

    /**
     * @param QueryBuilder $queryBuilder
     */
    public function setQueryBuilder(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
}

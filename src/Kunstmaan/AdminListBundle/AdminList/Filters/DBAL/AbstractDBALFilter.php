<?php
namespace Kunstmaan\AdminListBundle\AdminList\Filters\DBAL;

use Kunstmaan\AdminListBundle\AdminList\Filters\AbstractFilter;

use Doctrine\DBAL\Query\QueryBuilder;

/**
 * the abstract filter used for DBAL query builder
 */
abstract class AbstractDBALFilter extends AbstractFilter
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

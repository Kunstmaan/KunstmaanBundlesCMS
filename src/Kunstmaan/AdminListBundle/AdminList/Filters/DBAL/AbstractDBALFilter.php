<?php
namespace Kunstmaan\AdminListBundle\AdminList\Filters\DBAL;

use Kunstmaan\AdminListBundle\AdminList\Filters\AbstractFilter;
use Doctrine\DBAL\Query\QueryBuilder;

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

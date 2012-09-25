<?php
namespace Kunstmaan\AdminListBundle\AdminList\Filters\ORM;

use Kunstmaan\AdminListBundle\AdminList\Filters\AbstractFilter;
use Doctrine\ORM\QueryBuilder;

abstract class AbstractORMFilter extends AbstractFilter
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

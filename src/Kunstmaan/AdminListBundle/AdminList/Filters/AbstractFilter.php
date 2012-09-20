<?php

namespace Kunstmaan\AdminListBundle\AdminList\Filters;

use Doctrine\ORM\QueryBuilder;

use Symfony\Component\HttpFoundation\Request;

/**
 * AbstractFilter
 *
 * Abstract base class for all admin list filters
 */
abstract class AbstractFilter implements AdminListFilterInterface
{
    protected $columnName = null;
    protected $alias = null;

    /**
     * @param string $columnName The column name
     * @param string $alias      The alias
     */
    public function __construct($columnName, $alias = "b")
    {
        $this->columnName = $columnName;
        $this->alias      = $alias;
    }

    /**
     * @param Request $request  The request
     * @param array   &$data    The data
     * @param string  $uniqueId The unique identifier
     */
    abstract public function bindRequest(Request $request, &$data, $uniqueId);

    /**
     * @param QueryBuilder $queryBuilder The query builder
     * @param array        &$expressions The expression
     * @param array        $data         Data
     * @param string       $uniqueId     The identifier
     */
    abstract public function adaptQueryBuilder(QueryBuilder $queryBuilder, &$expressions, $data, $uniqueId);

    /**
     * @return string
     */
    abstract public function getTemplate();
}

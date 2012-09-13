<?php

namespace Kunstmaan\AdminListBundle\AdminList\Filters;

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

    public function __construct($columnName, $alias = "b")
    {
        $this->columnName = $columnName;
        $this->alias      = $alias;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param array                                     $data
     * @param string                                    $uniqueId
     */
    abstract public function bindRequest(Request $request, &$data, $uniqueId);

    /**
     * @param        $queryBuilder
     * @param array  $expressions
     * @param array  $data
     * @param string $uniqueId
     */
    abstract public function adaptQueryBuilder($queryBuilder, &$expressions, $data, $uniqueId);

    /**
     * @return string
     */
    abstract public function getTemplate();
}

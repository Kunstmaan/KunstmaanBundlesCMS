<?php

namespace Kunstmaan\AdminListBundle\AdminList;

use Doctrine\ORM\QueryBuilder;
use Kunstmaan\AdminListBundle\AdminList\Provider\ProviderInterface;

use Symfony\Component\HttpFoundation\Request;

/**
 * Filter
 */
class Filter
{
    /**
     * @var string $columnName
     */
    protected $columnName = null;

    /**
     * @var array
     */
    protected $filterDefinition = null;

    /**
     * @var string
     */
    protected $uniqueId = null;

    /**
     * @var array
     */
    protected $data = array();

    /**
     * @param string $columnName       The column name
     * @param array  $filterDefinition The filter configuration
     * @param string $uniqueId         The unique id
     */
    public function __construct($columnName, $filterDefinition, $uniqueId)
    {
        $this->columnName       = $columnName;
        $this->filterDefinition = $filterDefinition;
        $this->uniqueId         = $uniqueId;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function bindRequest(Request $request)
    {
        $this->filterDefinition['type']->bindRequest($request, $this->data, $this->uniqueId);
    }

    /**
     * @param QueryBuilder $querybuilder The query builder
     * @param array        &$expressions The expressions
     */
    public function adaptQueryBuilder(QueryBuilder $querybuilder, &$expressions)
    {
        $this->filterDefinition['type']->adaptQueryBuilder($querybuilder, $expressions, $this->data, $this->uniqueId);
    }

    /**
     * Apply the filter
     */
    public function applyFilter(ProviderInterface $provider)
    {
        $this->filterDefinition['type']->applyFilter($provider, $this->data, $this->uniqueId);
    }

    /**
     * @return string
     */
    public function getColumnName()
    {
        return $this->columnName;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return Filter
     */
    public function getType()
    {
        return $this->filterDefinition['type'];
    }

    /**
     * @return string
     */
    public function getUniqueId()
    {
        return $this->uniqueId;
    }
}

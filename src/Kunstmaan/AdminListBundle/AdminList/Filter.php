<?php

namespace Kunstmaan\AdminListBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\FilterType\FilterTypeInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Filter
 */
class Filter
{
    /**
     * @var string
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
    public function __construct($columnName, array $filterDefinition, $uniqueId)
    {
        $this->columnName = $columnName;
        $this->filterDefinition = $filterDefinition;
        $this->uniqueId = $uniqueId;
    }

    /**
     * @param Request $request
     */
    public function bindRequest(Request $request)
    {
        /* @var FilterTypeInterface $type */
        $type = $this->filterDefinition['type'];
        $type->bindRequest($request, $this->data, $this->uniqueId);
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
     * @return FilterTypeInterface
     */
    public function getType()
    {
        return $this->filterDefinition['type'];
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->filterDefinition['options'];
    }

    /**
     * Apply the filter
     */
    public function apply()
    {
        $this->getType()->apply($this->getData(), $this->getUniqueId());
    }

    /**
     * @return string
     */
    public function getUniqueId()
    {
        return $this->uniqueId;
    }
}

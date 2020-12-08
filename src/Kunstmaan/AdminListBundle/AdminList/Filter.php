<?php

namespace Kunstmaan\AdminListBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\FilterType\FilterTypeInterface;
use Symfony\Component\HttpFoundation\Request;

class Filter
{
    /**
     * @var string
     */
    protected $columnName;

    /**
     * @var array
     */
    protected $filterDefinition;

    /**
     * @var string
     */
    protected $uniqueId;

    /**
     * @var array
     */
    protected $data = [];

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

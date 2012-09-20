<?php

namespace Kunstmaan\AdminListBundle\AdminList;

use Symfony\Component\HttpFoundation\Request;

class Filter
{
    /* @var string $columnName */
    protected $columnName = null;

    protected $filterDefinition = null;

    protected $uniqueId = null;

    protected $data = array();

    /**
     * @param string $columnName
     * @param array  $filterDefinition
     * @param string $uniqueId
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
     * @param $querybuilder
     * @param array $expressions
     */
    public function adaptQueryBuilder($querybuilder, &$expressions)
    {
        $this->filterDefinition['type']->adaptQueryBuilder($querybuilder, $expressions, $this->data, $this->uniqueId);
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

    public function getUniqueId()
    {
        return $this->uniqueId;
    }
}

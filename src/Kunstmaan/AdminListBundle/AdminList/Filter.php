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

    public function __construct($columnName, $filterDefinition, $uniqueId)
    {
        $this->columnName       = $columnName;
        $this->filterDefinition = $filterDefinition;
        $this->uniqueId         = $uniqueId;
    }

    public function bindRequest(Request $request)
    {
        $this->filterDefinition['type']->bindRequest($request, $this->data, $this->uniqueId);
    }

    public function adaptQueryBuilder($querybuilder, &$expressions)
    {
        $this->filterDefinition['type']->adaptQueryBuilder($querybuilder, $expressions, $this->data, $this->uniqueId);
    }

    public function getColumnName()
    {
        return $this->columnName;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getType()
    {
        return $this->filterDefinition['type'];
    }

    public function getUniqueId()
    {
        return $this->uniqueId;
    }
}

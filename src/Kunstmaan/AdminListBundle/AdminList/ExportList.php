<?php

namespace Kunstmaan\AdminListBundle\AdminList;

class ExportList implements ExportableInterface
{
    private $configurator;

    public function __construct($configurator)
    {
        $this->configurator = $configurator;
        $this->configurator->buildExportFields();
        $this->configurator->buildIterator();
    }

    public function getExportColumns()
    {
        return $this->configurator->getExportFields();
    }

    public function getAllIterator()
    {
        return $this->configurator->getIterator();
    }

    public function getStringValue($item, $columnName)
    {
        return $this->configurator->getStringValue($item, $columnName);
    }
}

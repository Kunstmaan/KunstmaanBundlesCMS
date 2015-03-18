<?php

namespace Kunstmaan\AdminListBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\Configurator\ExportListConfiguratorInterface;

class ExportList implements ExportableInterface
{
    /**
     * @var ExportListConfiguratorInterface
     */
    private $configurator;

    public function __construct(ExportListConfiguratorInterface $configurator)
    {
        $this->configurator = $configurator;
        $this->configurator->buildExportFields();
        $this->configurator->buildIterator();
    }

    public function getExportColumns()
    {
        return $this->configurator->getExportFields();
    }

    public function getIterator()
    {
        return $this->configurator->getIterator();
    }

    public function getStringValue($item, $columnName)
    {
        return $this->configurator->getStringValue($item, $columnName);
    }
}

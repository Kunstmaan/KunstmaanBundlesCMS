<?php

namespace Kunstmaan\AdminListBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\Configurator\ExportListConfiguratorInterface;
use Symfony\Component\HttpFoundation\Request;

class ExportList implements ExportableInterface
{
    /**
     * @var ExportListConfiguratorInterface
     */
    private $configurator;

    public function __construct(ExportListConfiguratorInterface $configurator)
    {
        $this->configurator = $configurator;
        $this->configurator->buildFilters();
        $this->configurator->buildExportFields();
        $this->configurator->buildIterator();
    }

    public function bindRequest(Request $request)
    {
        $this->configurator->bindRequest($request);
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

<?php

namespace Kunstmaan\AdminListBundle\AdminList\Configurator;

interface ExportListConfiguratorInterface
{
    public function getExportFields();

    public function addExportField($name, $header);

    public function buildIterator();

    public function getIterator();

    public function buildFilters();

    public function buildExportFields();

    public function getStringValue($item, $columnName);
}

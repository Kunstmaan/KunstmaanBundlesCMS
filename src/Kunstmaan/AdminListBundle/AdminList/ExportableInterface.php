<?php

namespace Kunstmaan\AdminListBundle\AdminList;

interface ExportableInterface
{
    public function getExportColumns();

    public function getAllIterator();

    public function getStringValue($item, $columnName);
}

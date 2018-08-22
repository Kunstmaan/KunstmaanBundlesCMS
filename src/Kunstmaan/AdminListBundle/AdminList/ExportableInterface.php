<?php

namespace Kunstmaan\AdminListBundle\AdminList;

interface ExportableInterface
{
    /**
     * @return array
     */
    public function getExportColumns();

    /**
     * @return \Iterator
     */
    public function getIterator();

    /**
     * @param array|object $item
     * @param string       $columnName
     *
     * @return string
     */
    public function getStringValue($item, $columnName);
}

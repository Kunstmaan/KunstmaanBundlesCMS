<?php

namespace Kunstmaan\AdminListBundle\AdminList\ListAction;

/**
 * CsvExportListAction
 */
class CsvExportListAction implements ListActionInterface
{

    /**
     * @return array
     */
    public function getUrl()
    {
        return array(
            'path'      => 'KunstmaanAdminListBundle_admin_export',
            'params'    => array(
                '_format'   => 'csv'
            )
        );
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return 'Export as CSV';
    }

    /**
     * @return null
     */
    public function getIcon()
    {
        return null;
    }

    /**
     * @return null
     */
    public function getTemplate()
    {
        return null;
    }
}

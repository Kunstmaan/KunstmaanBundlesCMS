<?php

namespace Kunstmaan\AdminListBundle\AdminList\Actions;

use Kunstmaan\AdminListBundle\AdminList\ListActionInterface;

class CsvExportAction implements ListActionInterface
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

<?php

namespace Kunstmaan\AdminListBundle\AdminList\Actions;

use Kunstmaan\AdminListBundle\AdminList\ListActionInterface;

class CsvExportAction implements ListActionInterface
{

    public function getUrl()
    {
        return array(
            'path'      => 'KunstmaanAdminListBundle_admin_export',
            'params'    => array(
                '_format'   => 'csv'
            )
        );
    }

    public function getLabel()
    {
        return 'Export as CSV';
    }

    public function getIcon()
    {
        return null;
    }

    public function getTemplate()
    {
        return null;
    }
}

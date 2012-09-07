<?php

namespace Kunstmaan\AdminListBundle\AdminList;

use Kunstmaan\AdminBundle\Helper\Acl\AclHelper;

class AdminListFactory
{
    public function createList(AbstractAdminListConfigurator $configurator, $em, $queryparams = array())
    {
        return new AdminList($configurator, $em, $queryparams);
    }
}

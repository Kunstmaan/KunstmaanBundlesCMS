<?php

namespace Kunstmaan\AdminListBundle\AdminList;

use Doctrine\ORM\EntityManager;

class AdminListFactory
{
    /**
     * @param AbstractAdminListConfigurator $configurator
     * @param EntityManager                 $em
     * @param array                         $queryparams
     *
     * @return AdminList
     */
    public function createList(AbstractAdminListConfigurator $configurator, EntityManager $em, $queryParams = array())
    {
        return new AdminList($configurator, $em, $queryParams);
    }
}

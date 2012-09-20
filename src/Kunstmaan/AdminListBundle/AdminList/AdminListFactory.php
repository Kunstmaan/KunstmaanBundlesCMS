<?php

namespace Kunstmaan\AdminListBundle\AdminList;

use Doctrine\ORM\EntityManager;

/**
 * AdminListFactory
 */
class AdminListFactory
{
    /**
     * @param AbstractAdminListConfigurator $configurator The configurator
     * @param EntityManager                 $em           The entity manager
     * @param array                         $queryParams  The query parameters
     *
     * @return AdminList
     */
    public function createList(AbstractAdminListConfigurator $configurator, EntityManager $em, $queryParams = array())
    {
        return new AdminList($configurator, $em, $queryParams);
    }
}

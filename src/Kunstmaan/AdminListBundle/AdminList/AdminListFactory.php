<?php

namespace Kunstmaan\AdminListBundle\AdminList;

/**
 * AdminListFactory
 */
class AdminListFactory
{
    /**
     * @param AdminListConfiguratorInterface $configurator The configurator
     *
     * @return AdminList
     */
    public function createList(AdminListConfiguratorInterface $configurator)
    {
        return new AdminList($configurator);
    }
}

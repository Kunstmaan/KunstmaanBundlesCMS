<?php

namespace Kunstmaan\UserManagementBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\StringFilterType;

/**
 * Role admin list configurator used to manage {@link Role} in the admin
 */
class RoleAdminListConfigurator extends AbstractSettingsAdminListConfigurator
{
    /**
     * Build filters for admin list
     */
    public function buildFilters()
    {
        $this->addFilter('role', new StringFilterType('role'), 'kuma_user.role.adminlist.filter.role');
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('role', 'kuma_user.role.adminlist.header.role', true);
    }

    /**
     * Get repository name
     *
     * @return string
     */
    public function getEntityName()
    {
        return 'Role';
    }
}

<?php

namespace Kunstmaan\UserManagementBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\BooleanFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\StringFilterType;

/**
 * User admin list configurator used to manage {@link User} in the admin
 */
class UserAdminListConfigurator extends AbstractSettingsAdminListConfigurator
{
    /**
     * Build filters for admin list
     */
    public function buildFilters()
    {
        $this->addFilter('username', new StringFilterType('username'), 'kuma_user.users.adminlist.filter.username');
        $this->addFilter('email', new StringFilterType('email'), 'kuma_user.users.adminlist.filter.email');
        $this->addFilter('enabled', new BooleanFilterType('enabled'), 'kuma_user.users.adminlist.filter.enabled');
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('username', 'kuma_user.users.adminlist.header.username', true);
        $this->addField('email', 'kuma_user.users.adminlist.header.email', true);
        $this->addField('enabled', 'kuma_user.users.adminlist.header.enabled', true);
        $this->addField('lastLogin', 'kuma_user.users.adminlist.header.last_login', false);
        $this->addField('groups', 'kuma_user.users.adminlist.header.groups', false);
    }

    /**
     * Override path convention (because settings is a virtual admin subtree)
     *
     * @param string $suffix
     *
     * @return string
     */
    public function getPathByConvention($suffix = null)
    {
        return 'KunstmaanUserManagementBundle_settings_users' . (empty($suffix) ? '' : '_' . $suffix);
    }

    /**
     * Get entity name
     *
     * @return string
     */
    public function getEntityName()
    {
        return 'User';
    }
}

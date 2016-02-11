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
        $this->addFilter('username', new StringFilterType('username'), 'Username');
        $this->addFilter('email', new StringFilterType('email'), 'E-Mail');
        $this->addFilter('enabled', new BooleanFilterType('enabled'), 'Enabled');
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('username', 'Username', true);
        $this->addField('email', 'E-Mail', true);
        $this->addField('enabled', 'Enabled', true);
        $this->addField('lastLogin', 'Last Login', false);
        $this->addField('groups', 'Groups', false);
    }

    /**
     * Override path convention (because settings is a virtual admin subtree)
     *
     * @param string $suffix
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

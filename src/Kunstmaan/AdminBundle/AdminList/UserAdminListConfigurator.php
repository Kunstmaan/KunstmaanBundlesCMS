<?php

namespace Kunstmaan\AdminBundle\AdminList;

use Kunstmaan\AdminBundle\Entity\User;
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
        $builder = $this->getFilterBuilder();
        $builder->add('username', new StringFilterType('username'), 'Username');
        $builder->add('email', new StringFilterType('email'), 'E-Mail');
        $builder->add('enabled', new BooleanFilterType('enabled'), 'Enabled');
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
     * Get entity name
     *
     * @return string
     */
    public function getEntityName()
    {
        return 'User';
    }
}

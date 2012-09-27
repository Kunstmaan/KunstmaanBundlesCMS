<?php

namespace Kunstmaan\AdminBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminListBundle\AdminList\Filters\ORM\BooleanFilter;
use Kunstmaan\AdminListBundle\AdminList\Filters\ORM\StringFilter;

/**
 * UserAdminListConfigurator
 *
 * @todo We should probably move this to the AdminList bundle to prevent circular references...
 */
class UserAdminListConfigurator extends AbstractSettingsAdminListConfigurator
{
    /**
     * Build filters for admin list
     *
     * @param AdminListFilter $builder
     */
    public function buildFilters()
    {
        $builder = $this->getAdminListFilter();
        $builder->add('username', new StringFilter('username'), 'Username');
        $builder->add('email', new StringFilter('email'), 'E-Mail');
        $builder->add('enabled', new BooleanFilter('enabled'), 'Enabled');
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
     * Get entity name.
     *
     * @return string
     */
    public function getEntityName()
    {
        return 'User';
    }
}

<?php

namespace Kunstmaan\AdminBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\Filters\ORM\StringFilter;

/**
 * GroupAdminListConfigurator
 *
 * @todo We should probably move this to the AdminList bundle to prevent circular references...
 */
class GroupAdminListConfigurator extends AbstractSettingsAdminListConfigurator
{

    /**
     * Build filters for admin list
     */
    public function buildFilters()
    {
        $this->getAdminListFilter()->add('name', new StringFilter('name'), 'Name');
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('name', 'Name', true);
        $this->addField('roles', 'Roles', false);
    }

    /**
     * Get repository name
     *
     * @return string
     */
    public function getEntityName()
    {
        return 'Group';
    }

}

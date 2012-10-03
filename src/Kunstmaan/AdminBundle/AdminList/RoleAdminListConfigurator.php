<?php

namespace Kunstmaan\AdminBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\StringFilterType;

use Symfony\Component\Form\AbstractType;

/**
 * RoleAdminListConfigurator
 *
 * @todo We should probably move this to the AdminList bundle to prevent circular references...
 */
class RoleAdminListConfigurator extends AbstractSettingsAdminListConfigurator
{

    /**
     * Build filters for admin list
     */
    public function buildFilters()
    {
        $this->getFilterBuilder()->add('role', new StringFilterType('role'), 'Role');
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('role', 'Role', true);
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

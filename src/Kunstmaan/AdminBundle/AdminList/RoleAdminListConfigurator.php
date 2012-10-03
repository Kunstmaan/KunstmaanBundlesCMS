<?php

namespace Kunstmaan\AdminBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminListBundle\AdminList\Filters\ORM\StringFilter;

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
     *
     * @param AdminListFilter $builder
     */
    public function buildFilters()
    {
        $this->getAdminListFilter()->add('role', new StringFilter('role'), 'Role');
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

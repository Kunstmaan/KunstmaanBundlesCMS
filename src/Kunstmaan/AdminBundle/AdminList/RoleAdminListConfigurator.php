<?php

namespace Kunstmaan\AdminBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminListBundle\AdminList\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\Filters\ORM\StringFilter;

/**
 * RoleAdminListConfigurator
 *
 * @todo We should probably move this to the AdminList bundle to prevent circular references...
 */
class RoleAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{

    /**
     * Build filters for admin list
     *
     * @param AdminListFilter $builder
     */
    public function buildFilters()
    {
        $this->getAdminListFilter()->add('role', new StringFilter("role"), "Role");
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField("role", "Role", true);
    }

    /**
     * Configure add action(s) of admin list
     *
     * @param array $params
     *
     * @return array
     */
    public function getAddUrlFor(array $params = array())
    {
        return array(
            'role' => array('path' => 'KunstmaanAdminBundle_settings_roles_add', 'params' => $params)
        );
    }

    /**
     * Configure edit action(s) of admin list
     *
     * @param mixed $item
     *
     * @return array
     */
    public function getEditUrlFor($item)
    {
        return array(
            'path'   => 'KunstmaanAdminBundle_settings_roles_edit',
            'params' => array('roleId' => $item->getId())
        );
    }

    /**
     * Configure index action of admin list
     *
     * @return array
     */
    public function getIndexUrlFor()
    {
        return array('path' => 'KunstmaanAdminBundle_settings_roles');
    }

    /**
     * Get admin type of entity
     *
     * @param mixed $item
     *
     * @return AbstractType|null
     */
    public function getAdminType($item)
    {
        return null;
    }

    /**
     * Configure delete action(s) of admin list
     *
     * @param mixed $item
     *
     * @return array
     */
    public function getDeleteUrlFor($item)
    {
        return array(
            'path'      => 'KunstmaanAdminBundle_settings_roles_delete',
            'params'    => array(
                'roleId'    => $item->getId()
            )
        );
    }

    /**
     * Get repository name
     *
     * @return string
     */
    public function getRepositoryName()
    {
        return 'KunstmaanAdminBundle:Role';
    }

}

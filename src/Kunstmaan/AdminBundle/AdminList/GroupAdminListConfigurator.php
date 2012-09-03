<?php

namespace Kunstmaan\AdminBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\StringFilterType;
use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;

use Symfony\Component\Form\AbstractType;

/**
 * @todo We should probably combine Admin & AdminList into 1 bundle, or move this to the AdminList bundle to prevent circular references...
 */
class GroupAdminListConfigurator extends AbstractAdminListConfigurator
{

    /**
     * @param AdminListFilter $builder
     */
    public function buildFilters(AdminListFilter $builder)
    {
        $builder->add('name', new StringFilterType("name"), "Name");
    }

    public function buildFields()
    {
        $this->addField("name", "Name", true);
        $this->addField("roles", "Roles", false);
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function getAddUrlFor($params = array())
    {
        return array(
            'group' => array('path' => 'KunstmaanAdminBundle_settings_groups_add', 'params'=> $params)
        );
    }

    /**
     * @param mixed $item
     *
     * @return array
     */
    public function getEditUrlFor($item)
    {
        return array(
            'path'   => 'KunstmaanAdminBundle_settings_groups_edit',
            'params' => array('groupId' => $item->getId())
        );
    }

    /**
     * @return array
     */
    public function getIndexUrlFor()
    {
        return array('path' => 'KunstmaanAdminBundle_settings_groups');
    }

    /**
     * @param mixed $item
     *
     * @return array
     */
    public function getDeleteUrlFor($item)
    {
        return array(
            'path'      => 'KunstmaanAdminBundle_settings_groups_delete',
            'params'    => array(
                'groupId'    => $item->getId()
            )
        );
    }

    /**
     * @param mixed $item
     *
     * @return AbstractType|null
     */
    public function getAdminType($item)
    {
        return null;
    }

    /**
     * @return string
     */
    public function getRepositoryName()
    {
        return 'KunstmaanAdminBundle:Group';
    }

}

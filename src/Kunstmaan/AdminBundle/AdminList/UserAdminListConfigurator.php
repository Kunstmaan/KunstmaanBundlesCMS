<?php

namespace Kunstmaan\AdminBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\BooleanFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\StringFilterType;

/**
 * @todo We should probably move this to the AdminList bundle to prevent circular references...
 */
class UserAdminListConfigurator extends AbstractAdminListConfigurator
{

    /**
     * @param AdminListFilter $builder
     */
    public function buildFilters(AdminListFilter $builder)
    {
        $builder->add('username', new StringFilterType("username"), "Username");
        $builder->add('email', new StringFilterType("email"), "E-Mail");
        $builder->add('enabled', new BooleanFilterType("enabled"), "Enabled");
    }

    /**
     *
     */
    public function buildFields()
    {
        $this->addField("username", "Username", true);
        $this->addField("email", "E-Mail", true);
        $this->addField("enabled", "Enabled", true);
        $this->addField("lastlogin", "Last Login", false);
        $this->addField("groups", "Groups", false);
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function getAddUrlFor($params = array())
    {
        return array(
            'user' => array('path' => 'KunstmaanAdminBundle_settings_users_add', 'params'=> $params)
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
            'path'   => 'KunstmaanAdminBundle_settings_users_edit',
            'params' => array('userId' => $item->getId())
        );
    }

    /**
     * @return array
     */
    public function getIndexUrlFor()
    {
        return array('path' => 'KunstmaanAdminBundle_settings_users');
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
     * @param mixed $item
     *
     * @return array
     */
    public function getDeleteUrlFor($item)
    {
        return array(
            'path'      => 'KunstmaanAdminBundle_settings_users_delete',
            'params'    => array(
                'userId'    => $item->getId()
            )
        );
    }

    /**
     * @return string
     */
    public function getRepositoryName()
    {
        return 'KunstmaanAdminBundle:User';
    }

}

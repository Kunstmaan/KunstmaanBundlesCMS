<?php

namespace Kunstmaan\AdminBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\DoctrineORMAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminListBundle\AdminList\Filters\ORM\BooleanFilter;
use Kunstmaan\AdminListBundle\AdminList\Filters\ORM\StringFilter;

/**
 * UserAdminListConfigurator
 *
 * @todo We should probably move this to the AdminList bundle to prevent circular references...
 */
class UserAdminListConfigurator extends DoctrineORMAdminListConfigurator
{

    /**
     * Build filters for admin list
     *
     * @param AdminListFilter $builder
     */
    public function buildFilters()
    {
        $builder = $this->getAdminListFilter();
        $builder->add('username', new StringFilter("username"), "Username");
        $builder->add('email', new StringFilter("email"), "E-Mail");
        $builder->add('enabled', new BooleanFilter("enabled"), "Enabled");
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField("username", "Username", true);
        $this->addField("email", "E-Mail", true);
        $this->addField("enabled", "Enabled", true);
        $this->addField("lastLogin", "Last Login", false);
        $this->addField("groups", "Groups", false);
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
            'user' => array('path' => 'KunstmaanAdminBundle_settings_users_add', 'params'=> $params)
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
            'path'   => 'KunstmaanAdminBundle_settings_users_edit',
            'params' => array('userId' => $item->getId())
        );
    }

    /**
     * Configure index action of admin list
     *
     * @return array
     */
    public function getIndexUrlFor()
    {
        return array('path' => 'KunstmaanAdminBundle_settings_users');
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
            'path'      => 'KunstmaanAdminBundle_settings_users_delete',
            'params'    => array(
                'userId'    => $item->getId()
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
        return 'KunstmaanAdminBundle:User';
    }

}

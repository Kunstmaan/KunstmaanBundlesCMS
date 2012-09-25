<?php

namespace Kunstmaan\AdminBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminListBundle\AdminList\Filters\DateFilter;
use Kunstmaan\AdminListBundle\AdminList\Filters\StringFilter;
use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;

/**
 * LogAdminListConfigurator
 *
 * @todo We should probably move this to the AdminList bundle to prevent circular references...
 */
class LogAdminListConfigurator extends AbstractAdminListConfigurator
{

    /**
     * Build filters for admin list
     *
     * @param AdminListFilter $builder
     */
    public function buildFilters(AdminListFilter $builder)
    {
        parent::buildFilters($builder);
        $builder->add('user', new StringFilter("user"), "User");
        $builder->add('status', new StringFilter("status"), "Status");
        $builder->add('message', new StringFilter("message"), "Message");
        $builder->add('createdat', new DateFilter("createdat"), "Created At");
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField("user", "User", true);
        $this->addField("status", "Status", true);
        $this->addField("message", "Message", true);
        $this->addField("createdat", "Created At", true);
    }

    /**
     * Determine if the user can add items
     *
     * @return bool
     */
    public function canAdd()
    {
        return false;
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
        return array();
    }

    /**
     * Determine if the user can edit the specified item
     *
     * @param mixed $item
     *
     * @return bool
     */
    public function canEdit($item)
    {
        return false;
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
        return array();
    }

    /**
     * Configure index action of admin list
     *
     * @return array
     */
    public function getIndexUrlFor()
    {
        return array('path' => 'KunstmaanAdminBundle_settings_logs');
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
        return array();
    }

    /**
     * Determine if the user can delete the specified item
     *
     * @param mixed $item
     *
     * @return bool
     */
    public function canDelete($item)
    {
        return false;
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
     * Get repository name
     *
     * @return string
     */
    public function getRepositoryName()
    {
        return 'KunstmaanAdminBundle:LogItem';
    }
}

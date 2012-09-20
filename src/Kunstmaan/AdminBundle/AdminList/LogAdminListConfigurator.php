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
     * @param AdminListFilter $builder
     */
    public function buildFilters(AdminListFilter $builder)
    {
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
     * @return bool
     */
    public function canAdd()
    {
        return false;
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function getAddUrlFor($params = array())
    {
        return array();
    }

    /**
     * @return bool
     */
    public function canEdit()
    {
        return false;
    }

    /**
     * @param mixed $item
     *
     * @return array
     */
    public function getEditUrlFor($item)
    {
        return array();
    }

    /**
     * @return array
     */
    public function getIndexUrlFor()
    {
        return array('path' => 'KunstmaanAdminBundle_settings_logs');
    }

    /**
     * @param mixed $item
     *
     * @return array
     */
    public function getDeleteUrlFor($item)
    {
        return array();
    }

    /**
     * @param mixed $item
     *
     * @return bool
     */
    public function canDelete($item)
    {
        return false;
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
        return 'KunstmaanAdminBundle:LogItem';
    }
}

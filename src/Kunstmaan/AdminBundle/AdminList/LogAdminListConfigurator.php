<?php

namespace Kunstmaan\AdminBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Doctrine\ORM\QueryBuilder;
use Kunstmaan\AdminListBundle\AdminList\DoctrineORMAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\Filters\ORM\DateFilter;
use Kunstmaan\AdminListBundle\AdminList\Filters\ORM\StringFilter;

/**
 * LogAdminListConfigurator
 *
 * @todo We should probably move this to the AdminList bundle to prevent circular references...
 */
class LogAdminListConfigurator extends DoctrineORMAdminListConfigurator
{

    /**
     * Build filters for admin list
     *
     * @param AdminListFilter $builder
     */
    public function buildFilters()
    {
        $builder = $this->getAdminListFilter();
        $builder->add('u.username', new StringFilter('username', 'u'), 'User');
        $builder->add('status', new StringFilter('status'), 'Status');
        $builder->add('message', new StringFilter('message'), 'Message');
        $builder->add('createdAt', new DateFilter('createdAt'), 'Created At');
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('u.username', 'User', true);
        $this->addField('status', 'Status', true);
        $this->addField('message', 'Message', true);
        $this->addField('createdAt', 'Created At', true);
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

    public function adaptQueryBuilder(QueryBuilder $queryBuilder)
    {
        $queryBuilder->leftJoin('b.user', 'u');
    }

    public function getValue($item, $columnName)
    {
        if ('u.username' == $columnName) {
            return $item->getUser()->getUsername();
        }

        return parent::getValue($item, $columnName);
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

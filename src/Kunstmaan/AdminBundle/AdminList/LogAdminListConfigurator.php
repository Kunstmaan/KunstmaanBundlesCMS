<?php

namespace Kunstmaan\AdminBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Doctrine\ORM\QueryBuilder;
use Kunstmaan\AdminListBundle\AdminList\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\Filters\ORM\DateFilter;
use Kunstmaan\AdminListBundle\AdminList\Filters\ORM\StringFilter;

/**
 * LogAdminListConfigurator
 *
 * @todo We should probably move this to the AdminList bundle to prevent circular references...
 */
class LogAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
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
            $user = $item->getUser();
            if (!is_null($user)) {
                return $user->getUsername();
            }

            return '';
        }

        return parent::getValue($item, $columnName);
    }

    /**
     * Get bundle name
     *
     * @return string
     */
    public function getBundleName()
    {
        return 'KunstmaanAdminBundle';
    }

    /**
     * Get entity name
     *
     * @return string
     */
    public function getEntityName()
    {
        return 'LogItem';
    }
}

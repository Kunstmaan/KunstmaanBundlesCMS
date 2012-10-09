<?php

namespace Kunstmaan\AdminBundle\AdminList;

use Doctrine\ORM\QueryBuilder;

use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Entity\LogItem;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\DateFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\StringFilterType;

use Symfony\Component\Form\AbstractType;

/**
 * Log admin list configurator used to manage {@link LogItem} in the admin
 */
class LogAdminListConfigurator extends AbstractSettingsAdminListConfigurator
{

    /**
     * Build filters for admin list
     */
    public function buildFilters()
    {
        $this->addFilter('u.username', new StringFilterType('username', 'u'), 'User');
        $this->addFilter('status', new StringFilterType('status'), 'Status');
        $this->addFilter('message', new StringFilterType('message'), 'Message');
        $this->addFilter('createdAt', new DateFilterType('createdAt'), 'Created At');
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('u.username', 'User', true)
             ->addField('status', 'Status', true)
             ->addField('message', 'Message', true)
             ->addField('createdAt', 'Created At', true);
    }

    /**
     * Return the url to list all the items
     *
     * @return array
     */
    public function getIndexUrl()
    {
        return array(
            'path' => 'KunstmaanAdminBundle_settings_logs',
            'params' => array()
        );
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

    /**
     * @param QueryBuilder $queryBuilder
     */
    public function adaptQueryBuilder(QueryBuilder $queryBuilder)
    {
        $queryBuilder->leftJoin('b.user', 'u');
    }

    /**
     * @param array|object $item       The item
     * @param string       $columnName The column name
     *
     * @return mixed
     */
    public function getValue($item, $columnName)
    {
        if ('u.username' == $columnName) {
            /* @var User $user */
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

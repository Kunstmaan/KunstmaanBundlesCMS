<?php

namespace Kunstmaan\MenuBundle\AdminList;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;

class MenuAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{
    /**
     * @param EntityManager $em        The entity manager
     * @param AclHelper     $aclHelper The acl helper
     */
    public function __construct(EntityManager $em, AclHelper $aclHelper = null)
    {
        parent::__construct($em, $aclHelper);
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('name', 'Name', true);
        $this->addField('locale', 'Locale', true);
    }

    /**
     * Build filters for admin list
     */
    public function buildFilters()
    {
        $this->addFilter('name', new ORM\StringFilterType('name'), 'Name');
        $this->addFilter('locale', new ORM\StringFilterType('locale'), 'Locale');
    }

    /**
     * Get bundle name
     *
     * @return string
     */
    public function getBundleName()
    {
        return 'KunstmaanMenuBundle';
    }

    /**
     * Get entity name
     *
     * @return string
     */
    public function getEntityName()
    {
        return 'Menu';
    }

    /**
     * @return bool
     */
    public function canAdd()
    {
        return false;
    }

    /**
     * @param object|array $item
     *
     * @return bool
     */
    public function canEdit($item)
    {
        return false;
    }

    /**
     * Configure if it's possible to delete the given $item
     *
     * @param object|array $item
     *
     * @return bool
     */
    public function canDelete($item)
    {
        return false;
    }
}

<?php

namespace Kunstmaan\CookieBundle\AdminList;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;
use Kunstmaan\AdminListBundle\AdminList\SortableInterface;
use Kunstmaan\CookieBundle\Form\CookieTypeAdminType;

/**
 * Class CookieTypeAdminListConfigurator
 */
class CookieTypeAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator implements SortableInterface
{
    /**
     * @param EntityManagerInterface $em        The entity manager
     * @param AclHelper              $aclHelper The acl helper
     */
    public function __construct(EntityManagerInterface $em, ?AclHelper $aclHelper = null)
    {
        parent::__construct($em, $aclHelper);
        $this->setAdminType(CookieTypeAdminType::class);
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('name', 'kuma.cookie.adminlists.cookie_type.name', true);
        $this->addField('internalName', 'kuma.cookie.adminlists.cookie_type.internal_name', true);
    }

    /**
     * Build filters for admin list
     */
    public function buildFilters()
    {
        $this->addFilter('name', new ORM\StringFilterType('name'), 'kuma.cookie.adminlists.cookie_type.name');
        $this->addFilter('internalName', new ORM\StringFilterType('internalName'), 'kuma.cookie.adminlists.cookie_type.internal_name');
    }

    /**
     * Get bundle name
     *
     * @return string
     */
    public function getBundleName()
    {
        return 'KunstmaanCookieBundle';
    }

    /**
     * Get entity name
     *
     * @return string
     */
    public function getEntityName()
    {
        return 'CookieType';
    }

    /**
     * Get sortable field name
     *
     * @return string
     */
    public function getSortableField()
    {
        return 'weight';
    }
}

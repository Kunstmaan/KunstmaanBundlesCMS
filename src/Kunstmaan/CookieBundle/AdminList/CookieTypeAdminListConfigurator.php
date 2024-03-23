<?php

namespace Kunstmaan\CookieBundle\AdminList;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;
use Kunstmaan\AdminListBundle\AdminList\SortableInterface;
use Kunstmaan\CookieBundle\Entity\CookieType;
use Kunstmaan\CookieBundle\Form\CookieTypeAdminType;

/**
 * Class CookieTypeAdminListConfigurator
 */
class CookieTypeAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator implements SortableInterface
{
    /**
     * @param EntityManager $em        The entity manager
     * @param AclHelper     $aclHelper The acl helper
     */
    public function __construct(EntityManager $em, ?AclHelper $aclHelper = null)
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

    public function getEntityClass(): string
    {
        return CookieType::class;
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

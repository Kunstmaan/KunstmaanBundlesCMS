<?php

namespace {{ namespace }}\AdminList;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;

{{ namespace }}\Form\SatelliteAdminType;

/**
 * The admin list configurator for Satellite
 */
class SatelliteAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{

    /**
     * @param EntityManager $em        The entity manager
     * @param AclHelper     $aclHelper The acl helper
     */
    public function __construct(EntityManager $em, AclHelper $aclHelper = null)
    {
        parent::__construct($em, $aclHelper);
        $this->setAdminType(new SatelliteAdminType());
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('name', 'name', true);
        $this->addField('launched', 'launched', true);
        $this->addField('link', 'link', true);
        $this->addField('weight', 'weight', true);
        $this->addField('type', 'type', true);
    }

    /**
     * Build filters for admin list
     */
    public function buildFilters()
    {
        $this->addFilter('name', new ORM\StringFilterType('name'), 'Name');
        $this->addFilter('launched', new ORM\DateFilterType('launched'), 'Launched');
        $this->addFilter('link', new ORM\StringFilterType('link'), 'Link');
        $this->addFilter('weight', new ORM\NumberFilterType('weight'), 'Weight');
        $this->addFilter('type', new ORM\StringFilterType('type'), 'Type');
    }

    /**
     * Get bundle name
     *
     * @return string
     */
    public function getBundleName()
    {
        return '{{ bundle_name }}';
    }

    /**
     * Get entity name
     *
     * @return string
     */
    public function getEntityName()
    {
        return 'Satellite';
    }

}

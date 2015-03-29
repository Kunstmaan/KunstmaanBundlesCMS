<?php

namespace {{ namespace }}\AdminList;

use Doctrine\ORM\EntityManager;
use {{ namespace }}\Entity\Bike;
use {{ namespace }}\Form\BikeAdminType;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;

class BikeAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{
    /**
     * @param EntityManager $em        The entity manager
     * @param AclHelper     $aclHelper The acl helper
     */
    public function __construct(EntityManager $em, AclHelper $aclHelper = null)
    {
	parent::__construct($em, $aclHelper);

	$this->setAdminType(new BikeAdminType());
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
	$this->addField('type', 'Type', true);
	$this->addField('brand', 'Brand', true);
	$this->addField('model', 'Model', true);
	$this->addField('price', 'Price', true);
    }

    /**
     * Build filters for admin list
     */
    public function buildFilters()
    {
	$this->addFilter('type', new ORM\EnumerationFilterType('type'), 'Type', array_combine(Bike::$types, Bike::$types));
	$this->addFilter('brand', new ORM\StringFilterType('brand'), 'Brand');
	$this->addFilter('model', new ORM\StringFilterType('model'), 'Model');
	$this->addFilter('price', new ORM\NumberFilterType('price'), 'Price');
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
	return 'Bike';
    }
}

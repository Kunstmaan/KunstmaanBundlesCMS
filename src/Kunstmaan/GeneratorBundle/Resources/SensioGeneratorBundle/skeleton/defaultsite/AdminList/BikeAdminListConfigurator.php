<?php

namespace {{ namespace }}\AdminList;

use {{ namespace }}\Entity\Bike;
use {{ namespace }}\Form\BikeAdminType;
use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;
use Kunstmaan\AdminListBundle\AdminList\SortableInterface;

class BikeAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator implements SortableInterface
{
    public function __construct(EntityManagerInterface $em, AclHelper $aclHelper = null)
    {
        parent::__construct($em, $aclHelper);

        $this->setAdminType(BikeAdminType::class);
    }

    public function buildFields(): void
    {
        $this->addField('type', 'Type', true);
        $this->addField('brand', 'Brand', true);
        $this->addField('model', 'Model', true);
        $this->addField('price', 'Price', true);
    }

    public function buildFilters(): void
    {
        $this->addFilter('type', new ORM\EnumerationFilterType('type'), 'Type', array_combine(Bike::$types, Bike::$types));
        $this->addFilter('brand', new ORM\StringFilterType('brand'), 'Brand');
        $this->addFilter('model', new ORM\StringFilterType('model'), 'Model');
        $this->addFilter('price', new ORM\NumberFilterType('price'), 'Price');
    }

    public function getEntityClass()
    {
        return Bike::class;
    }

    public function getSortableField(): string
    {
        return 'weight';
    }
}

<?php

namespace Kunstmaan\TaggingBundle\AdminList;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;
use Kunstmaan\TaggingBundle\Form\TagAdminType;

class TagAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{
    /**
     * @param EntityManager $em        The entity manager
     * @param AclHelper     $aclHelper The acl helper
     */
    public function __construct(EntityManager $em, AclHelper $aclHelper = null)
    {
        parent::__construct($em, $aclHelper);
        $this->setAdminType(TagAdminType::class);
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('name', 'kuma_tagging.adminlist.header.name', true);
        $this->addField('createdAt', 'kuma_tagging.adminlist.header.created_at', true);
        $this->addField('updatedAt', 'kuma_tagging.adminlist.header.updated_at', true);
    }

    /**
     * Build filters for admin list
     */
    public function buildFilters()
    {
        $this->addFilter('name', new ORM\StringFilterType('name'), 'kuma_tagging.adminlist.filter.name');
    }

    /**
     * Get bundle name
     *
     * @return string
     */
    public function getBundleName()
    {
        return 'KunstmaanTaggingBundle';
    }

    /**
     * Get entity name
     *
     * @return string
     */
    public function getEntityName()
    {
        return 'Tag';
    }
}

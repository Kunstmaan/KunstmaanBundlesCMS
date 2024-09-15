<?php

namespace Kunstmaan\TaggingBundle\AdminList;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;
use Kunstmaan\TaggingBundle\Entity\Tag;
use Kunstmaan\TaggingBundle\Form\TagAdminType;

class TagAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{
    /**
     * @param EntityManagerInterface $em        The entity manager
     * @param AclHelper              $aclHelper The acl helper
     */
    public function __construct(EntityManagerInterface $em, ?AclHelper $aclHelper = null)
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
        trigger_deprecation('kunstmaan/tagging-bundle', '6.4', 'The "%s" method is deprecated and will be removed in 7.0. Use the "getEntityClass" method instead.', __METHOD__);

        return 'KunstmaanTaggingBundle';
    }

    /**
     * Get entity name
     *
     * @return string
     */
    public function getEntityName()
    {
        trigger_deprecation('kunstmaan/tagging-bundle', '6.4', 'The "%s" method is deprecated and will be removed in 7.0. Use the "getEntityClass" method instead.', __METHOD__);

        return 'Tag';
    }

    public function getEntityClass(): string
    {
        return Tag::class;
    }

    /**
     * @param string|null $suffix
     *
     * @return string
     */
    public function getPathByConvention($suffix = null)
    {
        if (null === $suffix || $suffix === '') {
            return 'kunstmaantaggingbundle_admin_tag';
        }

        return sprintf('kunstmaantaggingbundle_admin_tag_%s', $suffix);
    }
}

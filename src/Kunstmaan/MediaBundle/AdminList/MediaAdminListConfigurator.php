<?php

namespace Kunstmaan\MediaBundle\AdminList;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\MediaBundle\Form\Type\MediaType;
use Kunstmaan\MediaBundle\Helper\MediaManager;

/**
 * The admin list configurator for the Media entity
 */
class MediaAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{
    /**
     * @param EntityManager $em        The entity manager
     * @param AclHelper     $aclHelper The acl helper
     * @param MediaManager  $mediaManager The media manager
     */
    public function __construct(EntityManager $em, AclHelper $aclHelper = null, MediaManager $mediaManager)
    {
        parent::__construct($em, $aclHelper);
        $this->setAdminType(new MediaType($mediaManager, $em));
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('name', 'Name', true);
        $this->addField('content_type', 'Type', true);
        $this->addField('updated_at', 'Date', true);
        $this->addField('filesize', 'Filesize', true);
    }

    /**
     * Build filters for admin list
     */
    public function buildFilters()
    {
        $this->addFilter('name', new ORM\StringFilterType('name'), 'Name');
        $this->addFilter('updated_at', new ORM\DateFilterType('date'), 'Date');
    }

    /**
     * Get bundle name
     *
     * @return string
     */
    public function getBundleName()
    {
        return 'KunstmaanMediaBundle';
    }

    /**
     * Get entity name
     *
     * @return string
     */
    public function getEntityName()
    {
        return 'Media';
    }

    /**
     * @param QueryBuilder $queryBuilder
     */
    public function adaptQueryBuilder(QueryBuilder $queryBuilder)
    {
        $queryBuilder->orderBy('b.updated_at', 'DESC');
    }

}

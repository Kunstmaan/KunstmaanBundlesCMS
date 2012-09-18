<?php

namespace Kunstmaan\MediaBundle\AdminList;

use Doctrine\ORM\QueryBuilder;

use Kunstmaan\MediaBundle\Entity\Video;

use Kunstmaan\MediaBundle\Entity\Folder;

use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\DateFilterType;
use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\StringFilterType;

/**
 * VideoListConfigurator
 */
class VideoListConfigurator extends AbstractAdminListConfigurator
{

    /**
     * @var Folder
     */
    private $folder;

    /**
     * @param Folder $folder
     */
    public function __construct(Folder $folder)
    {
        $this->folder = $folder;
    }

    /**
     * @param AdminListFilter $filters
     */
    public function buildFilters(AdminListFilter $filters)
    {
        $filters->add('name', new StringFilterType("name"), "form.name");
        $filters->add('type', new StringFilterType("type"), "form.type");
        $filters->add('createdAt', new DateFilterType("createdAt"), "form.createdat");
        $filters->add('updatedAt', new DateFilterType("updatedAt"), "form.updatedat");
    }

    /**
     * buildFields
     */
    public function buildFields()
    {
        $this->addField("name", "form.name", true);
        $this->addField("type", "form.type", true);
        $this->addField("createdAt", "form.createdat", true);
        $this->addField("updatedAt", "form.updatedat", true);
    }

    /**
     * @return bool
     */
    public function canAdd()
    {
        return false;
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function getAddUrlFor($params = array())
    {
        return array(
            'video' => array(
                'path'   => 'KunstmaanMediaBundle_folder_videocreate',
                'params' => array(
                    'gallery_id' => $params['gallery_id']
                )
            )
        );
    }

    /**
     * @param Video $item
     *
     * @return array
     */
    public function getEditUrlFor($item)
    {
        return array('path'   => 'KunstmaanMediaBundle_media_show',
                     'params' => array('media_id' => $item->getId())
        );
    }

    /**
     * @return array
     */
    public function getIndexUrlFor()
    {
        return array('path' => 'KunstmaanMediaBundle_folder_show', 'params' => array('id' => $this->folder->getId()));
    }

    /**
     * @return string
     */
    public function getRepositoryName()
    {
        return 'KunstmaanMediaBundle:Video';
    }


    /**
     * @param QueryBuilder $queryBuilder The query builder
     * @param array        $params       Custom parameters
     */
    public function adaptQueryBuilder($queryBuilder, $params = array())
    {
        parent::adaptQueryBuilder($queryBuilder);
        $queryBuilder->andwhere($queryBuilder->expr()->eq("b.gallery", $params['gallery']));
        $queryBuilder->andwhere("b.deleted != true");
    }

    /**
     * @param Video $item
     *
     * @return array
     */
    public function getDeleteUrlFor($item)
    {
        return array(
            'path'      => 'KunstmaanMediaBundle_media_delete',
            'params'    => array(
                'media_id'    => $item->getId()
            )
        );
    }
}

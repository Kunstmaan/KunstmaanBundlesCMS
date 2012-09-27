<?php

namespace Kunstmaan\MediaBundle\AdminList;

use Kunstmaan\MediaBundle\Entity\Folder;
use Doctrine\ORM\QueryBuilder;

use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminListBundle\AdminList\Filters\StringFilter;

/**
 * MediaListConfigurator
 */
class MediaListConfigurator extends AbstractAdminListConfigurator
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
        $filters->add('name', new StringFilter("name"), "form.name");
        $filters->add('classtype', new StringFilter("classtype"), "form.type");
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField("name", "form.name", true);
        $this->addField("classtype", "form.type", true);
        $this->addField("createdAt", "form.createdat", true);
        $this->addField("updatedAt", "form.updatedat", true);
    }

    /**
     * @return bool
     */
    public function canAdd()
    {
        return true;
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function getAddUrlFor(array $params = array())
    {
        return array(
            'image' => array(
                'path'   => 'KunstmaanMediaBundle_folder_imagecreate',
                'params' => array(
                    'folderId' => $params['folderId']
                )
            ),
            'file'  => array(
                'path'   => 'KunstmaanMediaBundle_folder_filecreate',
                'params' => array(
                    'folderId' => $params['folderId']
                )
            ),
            'slide' => array(
                'path'   => 'KunstmaanMediaBundle_folder_slidecreate',
                'params' => array(
                    'folderId' => $params['folderId']
                )
            ),
            'video' => array(
                'path'   => 'KunstmaanMediaBundle_folder_videocreate',
                'params' => array(
                    'folderId' => $params['folderId']
                )
            )
        );
    }

    /**
     * @param Folder $item
     *
     * @return array
     */
    public function getEditUrlFor($item)
    {
        return array(
            'path'   => 'KunstmaanMediaBundle_media_show',
            'params' => array(
                'mediaId' => $item->getId()
            )
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
        return 'KunstmaanMediaBundle:Media';
    }

    /**
     * @param QueryBuilder $queryBuilder The query builder
     * @param array        $params       Custom parameters
     */
    public function adaptQueryBuilder(QueryBuilder $queryBuilder, array $params = array())
    {
        parent::adaptQueryBuilder($queryBuilder, $params);
        $queryBuilder->andwhere($queryBuilder->expr()->eq("b.folder", $params['folder']));
        $queryBuilder->andwhere("b.deleted != true");
    }

    /**
     * @param Folder $item
     *
     * @return array
     */
    public function getDeleteUrlFor($item)
    {
        return array(
            'path'      => 'KunstmaanMediaBundle_media_delete',
            'params'    => array(
                'mediaId'    => $item->getId()
            )
        );
    }
}

<?php

namespace Kunstmaan\MediaBundle\AdminList;

use Doctrine\ORM\QueryBuilder;

use Kunstmaan\MediaBundle\Entity\File;

use Kunstmaan\MediaBundle\Entity\Folder;

use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminListBundle\AdminList\Filters\StringFilter;

/**
 * FileListConfigurator
 */
class FileListConfigurator extends AbstractAdminListConfigurator
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
        $filters->add('contentType', new StringFilter("contentType"), "form.type");
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField("name", "form.name", true);
        $this->addField("contentType", "form.type", true);
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
            'file' => array(
                'path'   => 'KunstmaanMediaBundle_folder_filecreate',
                'params' => array('gallery_id' => $params['gallery_id'])
            )
        );
    }

    /**
     * @param File $item
     *
     * @return array
     */
    public function getEditUrlFor($item)
    {
        return array(
            'path'   => 'KunstmaanMediaBundle_media_show',
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
        return 'KunstmaanMediaBundle:File';
    }

    /**
     * @param QueryBuilder $querybuilder The query builder
     * @param array        $params       Custom parameters
     */
    public function adaptQueryBuilder(QueryBuilder $querybuilder, $params = array())
    {
        parent::adaptQueryBuilder($querybuilder, $params);
        $querybuilder->andwhere($querybuilder->expr()->eq("b.gallery", $params['gallery']));
        $querybuilder->andwhere("b.deleted != true");
    }

    /**
     * @param File $item
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

<?php

namespace Kunstmaan\MediaBundle\Helper\MediaList;

use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\DateFilterType;
use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\StringFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\BooleanFilterType;

class VideoListConfigurator extends AbstractAdminListConfigurator
{

    private $folder;

    public function __construct($folder){
        $this->folder = $folder;
    }

    public function buildFilters(AdminListFilter $builder)
    {
        $builder->add('name', new StringFilterType("name"), "form.name");
        $builder->add('type', new StringFilterType("type"), "form.type");
        $builder->add('createdAt', new DateFilterType("createdAt"), "form.createdat");
        $builder->add('updatedAt', new DateFilterType("updatedAt"), "form.updatedat");
    }

    public function buildFields()
    {
        $this->addField("name", "form.name", TRUE);
        $this->addField("type", "form.type", TRUE);
        $this->addField("createdAt", "form.createdat", TRUE);
        $this->addField("updatedAt", "form.updatedat", TRUE);
    }

    public function canAdd()
    {
        return false;
    }

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

    public function getEditUrlFor($item)
    {
        return array('path'   => 'KunstmaanMediaBundle_media_show',
                     'params' => array('media_id' => $item->getId())
        );
    }

    public function getIndexUrlFor()
    {
        return array('path' => 'KunstmaanMediaBundle_folder_show', 'params' => array('id' => $this->folder->getId()));
    }

    public function getRepositoryName()
    {
        return 'KunstmaanMediaBundle:Video';
    }

    function adaptQueryBuilder($querybuilder, $params = array())
    {
        parent::adaptQueryBuilder($querybuilder);
        $querybuilder->andwhere($querybuilder->expr()->eq("b.gallery", $params['gallery']));
        $querybuilder->andwhere("b.deleted != true");
    }

    function getDeleteUrlFor($item)
    {
        return array(
            'path'      => 'KunstmaanMediaBundle_media_delete',
            'params'    => array(
                'media_id'    => $item->getId()
            )
        );
    }
}

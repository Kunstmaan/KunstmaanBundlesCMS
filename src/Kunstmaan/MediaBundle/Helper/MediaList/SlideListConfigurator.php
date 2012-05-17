<?php

namespace Kunstmaan\MediaBundle\Helper\MediaList;

use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\DateFilterType;
use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\StringFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\BooleanFilterType;

class SlideListConfigurator extends AbstractAdminListConfigurator{

    public function buildFilters(AdminListFilter $builder){
        $builder->add('name', new StringFilterType("name"), "form.name");
        $builder->add('type', new StringFilterType("type"), "form.type");
        $builder->add('createdAt', new DateFilterType("createdAt"), "form.createdat");
        $builder->add('updatedAt', new DateFilterType("updatedAt"), "form.updatedat");
    }

    public function buildFields()
    {
    	$this->addField("name", "form.name", true);
    	$this->addField("type", "form.type", true);
    	$this->addField("createdAt", "form.createdat", true);
    	$this->addField("updatedAt", "form.updatedat", true); 
    }

	public function getAddUrlFor($params=array()) {
    	return array('slide' => array('path' => 'KunstmaanMediaBundle_folder_slidecreate', 'params' => array( 'gallery_id' => $params['gallery_id'])));
    }

    public function getEditUrlFor($item) {
    	return array('path' => 'KunstmaanMediaBundle_media_show', 'params' => array( 'media_id' => $item->getId()));
    }

    public function getRepositoryName(){
        return 'KunstmaanMediaBundle:Slide';
    }

    function adaptQueryBuilder($querybuilder, $params=array()){
        parent::adaptQueryBuilder($querybuilder);
        $querybuilder->andwhere($querybuilder->expr()->eq("b.gallery", $params['gallery']));
    }

    function getDeleteUrlFor($item)
    {
        // TODO: Implement getDeleteUrlFor() method.
    }
}

<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kristof
 * Date: 15/11/11
 * Time: 22:23
 * To change this template use File | Settings | File Templates.
 */

namespace Kunstmaan\MediaBundle\Helper\MediaList;

use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\DateFilterType;

use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\StringFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\BooleanFilterType;

class VideoListConfigurator extends AbstractAdminListConfigurator{

    public function buildFilters(AdminListFilter $builder){
        $builder->add('name', new StringFilterType("name"));
        $builder->add('type', new StringFilterType("type"));
        $builder->add('createdAt', new DateFilterType("createdAt"));
        $builder->add('updatedAt', new DateFilterType("updatedAt"));
    }

    public function buildFields()
    {
    	$this->addField("name", "form.name", true);
    	$this->addField("type", "form.type", true);
    	$this->addField("createdAt", "form.createdat", true);
    	$this->addField("updatedAt", "form.updatedat", true);  	
    }

    public function canAdd() {
        return true;
    }

	public function getAddUrlFor($params=array()) {
    	return array('video' => array('path' => 'KunstmaanMediaBundle_folder_videocreate', 'params' => array( 'gallery_id' => $params['gallery_id'])));
    }

    public function canEdit() {
    	return true;
    }
    
    public function getEditUrlFor($item) {
    	return array('path' => 'KunstmaanMediaBundle_media_show', 'params' => array( 'media_id' => $item->getId()));
    }
    
    public function canDelete($item) {
        return true;
    }

    public function getRepositoryName(){
        return 'KunstmaanMediaBundle:Video';
    }

    function adaptQueryBuilder($querybuilder, $params=array()){
        parent::adaptQueryBuilder($querybuilder);
        $querybuilder->andwhere($querybuilder->expr()->eq("b.gallery", $params['gallery']));
    }
}

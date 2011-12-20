<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kristof
 * Date: 15/11/11
 * Time: 22:23
 * To change this template use File | Settings | File Templates.
 */

namespace Kunstmaan\MediaBundle\Helper\MediaList;

use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\StringFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\BooleanFilterType;

class FileListConfigurator extends AbstractAdminListConfigurator{

    public function buildFilters(AdminListFilter $builder){
        $builder->add('name', new StringFilterType("name"));
        $builder->add('contentType', new StringFilterType("contentType"));
    }

    public function buildFields()
    {
    	$this->addField("name", "Name", true);
    	$this->addField("contentType", "Content Type", true);
    	$this->addField("createdAt", "Created At", true);
    	$this->addField("updatedAt", "Updated At", true); 	
    }

	public function canAdd() {
        return true;
    }

    public function getAddUrlFor() {
    	return "KunstmaanMediaBundle_file_create";
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
        return 'KunstmaanMediaBundle:File';
    }

    function adaptQueryBuilder($querybuilder){
        parent::adaptQueryBuilder($querybuilder);
    }
}

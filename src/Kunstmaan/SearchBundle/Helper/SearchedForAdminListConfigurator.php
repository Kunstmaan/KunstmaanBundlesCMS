<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kris
 * Date: 15/11/11
 * Time: 22:23
 * To change this template use File | Settings | File Templates.
 */

namespace Kunstmaan\SearchBundle\Helper;

use Kunstmaan\ViewBundle\Entity\SearchPage;

use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\DateFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\StringFilterType;
use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;

class SearchedForAdminListConfigurator extends AbstractAdminListConfigurator{

	public function buildFilters(AdminListFilter $builder){
        $builder->add('query', new StringFilterType("query"), "Query");
        $builder->add('createdat', new DateFilterType('createdat'), "Created At");
    }
    
	public function buildFields()
    {
    	$this->addField("query", "Query", true);
    	$this->addField("searchpage", "Search Page", true);
    	$this->addField("createdat", "Created At", false);
    }

	public function canAdd() {
        return false;
    }

	public function getAddUrlFor($params=array()) {
    	return array();

    }

    public function canEdit() {
    	return false;
    }
    
    public function getEditUrlFor($item) {
    	return array();
    }
    public function canDelete($item) {
        return false;
    }

    public function getAdminType($item) {
        return null;
    }

    public function getRepositoryName() {
        return 'KunstmaanSearchBundle:SearchedFor';
    }

    public function adaptQueryBuilder($querybuilder, $params=array()) {
        parent::adaptQueryBuilder($querybuilder);
        //not needed to change something here yet but already
    }


    public function getValue($item, $columnName) {
        $result = parent::getValue($item, $columnName);

        if($result instanceof SearchPage) {
        	if($result->getParent()){
        		return $result->getParent()->getTitle() . "/" . $result->getTitle();
        	}else{
        		return "/" . $result->getTitle();
        	}
        }
        
        if($result instanceof \Doctrine\ORM\PersistentCollection) {
            $results = "";
            foreach($result as $entry) {
                $results[] = $entry->getName();
            }
            if (empty($results)) {
                return "";
            }
            return implode(', ', $results);
        }

        return $result;
    }
}

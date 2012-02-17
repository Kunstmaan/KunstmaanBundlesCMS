<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kris
 * Date: 15/11/11
 * Time: 22:23
 * To change this template use File | Settings | File Templates.
 */

namespace Kunstmaan\AdminBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\DateFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\StringFilterType;
use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;

class ErrorLogAdminListConfigurator extends AbstractAdminListConfigurator{

	public function buildFilters(AdminListFilter $builder){
        $builder->add('channel', new StringFilterType("channel"), "Channel");
        $builder->add('level', new StringFilterType("level"), "Level");
        $builder->add('message', new StringFilterType("message"), "Message");
        $builder->add('createdat', new DateFilterType("createdat"), "Created At");
    }
    
	public function buildFields()
    {
    	$this->addField("channel", "Channel", true);
    	$this->addField("level", "Level", true);
    	$this->addField("message", "Message", true);
    	$this->addField("createdat", "Created At", true);
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
        return 'KunstmaanAdminBundle:ErrorLogItem';
    }

    public function adaptQueryBuilder($querybuilder, $params=array()) {
        parent::adaptQueryBuilder($querybuilder);
    }


    public function getValue($item, $columnName) {
        $result = parent::getValue($item, $columnName);

        if($result instanceof \Doctrine\ORM\PersistentCollection) {
            $results = "";
            foreach($result as $entry) {
                $results[] = $entry->getName();
            }

            return implode(', ', $results);
        }

        if(is_array($result)) {
            return implode(', ', $result);
        }

        return $result;
    }
}

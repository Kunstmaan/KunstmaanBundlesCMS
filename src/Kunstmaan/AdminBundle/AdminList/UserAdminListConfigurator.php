<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kris
 * Date: 15/11/11
 * Time: 22:23
 * To change this template use File | Settings | File Templates.
 */

namespace Kunstmaan\AdminBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\BooleanFilterType;

use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\DateFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\StringFilterType;
use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;

class UserAdminListConfigurator extends AbstractAdminListConfigurator{

	public function buildFilters(AdminListFilter $builder){
        $builder->add('username', new StringFilterType("username"));
        $builder->add('email', new StringFilterType("email"));
        $builder->add('enabled', new BooleanFilterType("email"));
    }
    
	public function buildFields()
    {
    	$this->addField("username", "Username", true);
    	$this->addField("email", "E-Mail", true);
    	$this->addField("enabled", "Enabled", true);
    	$this->addField("lastlogin", "Last Login", false);
    	$this->addField("groups", "Groups", false); 	
    }

	public function canAdd() {
        return true;
    }

    public function getAddUrlFor($params=array()) {
    	return array(
    			'user' => array('path' => 'KunstmaanAdminBundle_settings_users_add', 'params'=> $params)
    	);

    }

    public function canEdit() {
    	return true;
    }
    
    public function getEditUrlFor($item) {
    	return array('path' => 'KunstmaanAdminBundle_settings_users_edit', 'params' => array( 'user_id' => $item->getId()));
    }

    public function canDelete($item) {
        return true;
    }

    public function getAdminType($item) {
        return null;
    }

    public function getRepositoryName() {
        return 'KunstmaanAdminBundle:User';
    }

    public function adaptQueryBuilder($querybuilder, $params=array()) {
        parent::adaptQueryBuilder($querybuilder);
        //not needed to change something here yet but already
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

        return $result;
    }
}

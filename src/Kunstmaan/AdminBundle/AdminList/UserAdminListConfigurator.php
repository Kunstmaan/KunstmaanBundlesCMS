<?php

namespace Kunstmaan\AdminBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\BooleanFilterType;

use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\DateFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\StringFilterType;
use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;

class UserAdminListConfigurator extends AbstractAdminListConfigurator{

	public function buildFilters(AdminListFilter $builder){
        $builder->add('username', new StringFilterType("username"), "Username");
        $builder->add('email', new StringFilterType("email"), "E-Mail");
        $builder->add('enabled', new BooleanFilterType("enabled"), "Enabled");
    }

	public function buildFields()
    {
    	$this->addField("username", "Username", true);
    	$this->addField("email", "E-Mail", true);
    	$this->addField("enabled", "Enabled", true);
    	$this->addField("lastlogin", "Last Login", false);
    	$this->addField("groups", "Groups", false);
    }

    public function getAddUrlFor($params=array()) {
    	return array(
    			'user' => array('path' => 'KunstmaanAdminBundle_settings_users_add', 'params'=> $params)
    	);

    }

    public function getEditUrlFor($item) {
    	return array('path' => 'KunstmaanAdminBundle_settings_users_edit', 'params' => array( 'user_id' => $item->getId()));
    }

    public function getAdminType($item) {
        return null;
    }

    public function getRepositoryName() {
        return 'KunstmaanAdminBundle:User';
    }

}

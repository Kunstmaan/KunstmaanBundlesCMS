<?php

namespace Kunstmaan\AdminBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\DateFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\StringFilterType;
use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;

class GroupAdminListConfigurator extends AbstractAdminListConfigurator{

	public function buildFilters(AdminListFilter $builder){
        $builder->add('name', new StringFilterType("name"), "Name");
    }

	public function buildFields()
    {
    	$this->addField("name", "Name", true);
    	$this->addField("roles", "Roles", false);
    }

    public function getAddUrlFor($params=array()) {
    	return array(
    			'group' => array('path' => 'KunstmaanAdminBundle_settings_groups_add', 'params'=> $params)
    	);

    }

    public function getEditUrlFor($item) {
    	return array('path' => 'KunstmaanAdminBundle_settings_groups_edit', 'params' => array( 'group_id' => $item->getId()));
    }

    public function getIndexUrlFor()
    {
        return array('path' => 'KunstmaanAdminBundle_settings_groups');
    }

    public function getDeleteUrlFor($item) {
        return array(
            'path'      => 'KunstmaanAdminBundle_settings_groups_delete',
            'params'    => array(
                'group_id'    => $item->getId()
            )
        );
    }

    public function getAdminType($item) {
        return null;
    }

    public function getRepositoryName() {
        return 'KunstmaanAdminBundle:Group';
    }
}

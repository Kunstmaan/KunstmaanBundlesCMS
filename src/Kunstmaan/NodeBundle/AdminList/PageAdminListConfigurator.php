<?php

namespace Kunstmaan\AdminNodeBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\StringFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\DateFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\BooleanFilterType;

class PageAdminListConfigurator extends AbstractAdminListConfigurator{

    protected $permission;
    protected $user;
    protected $locale;

    public function __construct($user, $permission, $locale)
    {
        $this->permission   = $permission;
        $this->user         = $user;
        $this->locale		= $locale;
    }


    public function buildFilters(AdminListFilter $builder)
    {
        $builder->add('title', new StringFilterType("title"), "Title");
        $builder->add('online', new BooleanFilterType("online"), "Online");
        $builder->add('created', new DateFilterType("created"), "Created At");
        $builder->add('updated', new DateFilterType("updated"), "Updated At");
    }

    public function buildFields()
    {
    	$this->addField("title", "Title", true);
    	$this->addField("created", "Created At", true);
    	$this->addField("updated", "Updated At", true);
    	$this->addField("online", "Online", true);
    }

    public function getEditUrlFor($item)
    {
        return array('path' => 'KunstmaanAdminNodeBundle_pages_edit', 'params' => array( 'id' => $item->getNode()->getId()));
    }
    
    public function canAdd()
    {
    	return false;
    }

    public function getAddUrlFor($params=array()) {
    	return "";
    }

    public function canDelete($item)
    {
        return false;
    }

    function getDeleteUrlFor($item)
    {
        return array();
    }


    public function getRepositoryName()
    {
        return 'KunstmaanAdminNodeBundle:NodeTranslation';
    }

    function adaptQueryBuilder($querybuilder, $params=array()){
        parent::adaptQueryBuilder($querybuilder);
        $querybuilder->andWhere('b.node NOT IN (select p.id from Kunstmaan\AdminNodeBundle\Entity\Node p where p.deleted=1)');
        $querybuilder->andWhere('b.lang = :lang');
        $querybuilder->setParameter('lang', $this->locale);
        /*FIXME: not going to fix it now, this list must first be converted to a version changes list
        $querybuilder->andWhere('b.id IN (
            SELECT
                p.refId
            FROM
                Kunstmaan\AdminBundle\Entity\Permission p
            WHERE
                    p.refEntityname = b.node.refEntityname
                AND
                    p.permissions LIKE :permissions
                AND
                    p.refGroup IN(:groups)
        )');

        $querybuilder->setParameter('permissions', '%|'.$this->permission.':1|%');
        $querybuilder->setParameter('groups', $this->user->getGroupIds());
	*/
        return $querybuilder;
    }
}

<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kris
 * Date: 15/11/11
 * Time: 22:23
 * To change this template use File | Settings | File Templates.
 */

namespace Kunstmaan\FormBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\StringFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\DateFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\BooleanFilterType;

class FormPageAdminListConfigurator extends AbstractAdminListConfigurator{

    protected $permission;
    protected $user;

    public function __construct($user, $permission)
    {
        $this->permission   = $permission;
        $this->user         = $user;
    }


    public function buildFilters(AdminListFilter $builder)
    {
        $builder->add('title', new StringFilterType("title"), "Title");
        $builder->add('online', new BooleanFilterType("online"), "Online");
    }

    public function buildFields()
    {
    	$this->addField("title", "Title", true);
    }

	public function getEditUrlFor($item) {
    	return array('path' => 'KunstmaanFormBundle_formsubmissions_list', 'params' => array( 'nodetranslationid' => $item->getId()));
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

    public function getRepositoryName()
    {
        return 'KunstmaanAdminNodeBundle:NodeTranslation';
    }

    function adaptQueryBuilder($querybuilder, $params=array()){
        parent::adaptQueryBuilder($querybuilder);
        $querybuilder->innerJoin('b.node', 'n', 'WITH', 'b.node = n.id')
	        ->andWhere('n.id IN (
	        		SELECT p.refId FROM Kunstmaan\AdminBundle\Entity\Permission p WHERE p.refEntityname = ?1 AND p.permissions LIKE ?2 AND p.refGroup IN(?3))')
	        ->andWhere('n.id IN (
	        		SELECT m.id FROM Kunstmaan\FormBundle\Entity\FormSubmission s join s.node m)')
	        ->setParameter(1, 'Kunstmaan\AdminNodeBundle\Entity\Node')
	        ->setParameter(2, '%|'.$this->permission.':1|%')
	        ->setParameter(3, $this->user->getGroupIds())
	        ->addOrderBy('n.sequencenumber', 'DESC');

        return $querybuilder;
    }
}

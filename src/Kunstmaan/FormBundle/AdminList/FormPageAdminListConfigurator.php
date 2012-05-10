<?php

namespace Kunstmaan\FormBundle\AdminList;

use Kunstmaan\AdminBundle\Entity\Permission;

use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\StringFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\DateFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\BooleanFilterType;

/**
 * Adminlist for form pages
 */
class FormPageAdminListConfigurator extends AbstractAdminListConfigurator
{

    protected $permission;
    protected $user;

    /**
     * @param mixed      $user       The User
     * @param Permission $permission The permission
     */
    public function __construct($user, $permission)
    {
        $this->permission   = $permission;
        $this->user         = $user;
    }

    /**
     * {@inheritdoc}
     */
    public function buildFilters(AdminListFilter $builder)
    {
        $builder->add('title', new StringFilterType("title"), "Title");
        $builder->add('online', new BooleanFilterType("online"), "Online");
    }

    /**
     * {@inheritdoc}
     */
    public function buildFields()
    {
    	$this->addField("title", "Title", true);
    }

    /**
     * {@inheritdoc}
     */
	public function getEditUrlFor($item)
	{
    	return array('path' => 'KunstmaanFormBundle_formsubmissions_list', 'params' => array( 'nodetranslationid' => $item->getId()));
    }

    /**
     * {@inheritdoc}
     */
    public function canAdd()
    {
    	return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getAddUrlFor($params=array())
    {
    	return "";
    }

    /**
     * {@inheritdoc}
     */
    public function canDelete($item)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepositoryName()
    {
        return 'KunstmaanAdminNodeBundle:NodeTranslation';
    }

    /**
     * {@inheritdoc}
     */
    public function adaptQueryBuilder($querybuilder, $params=array())
    {
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

<?php

namespace Kunstmaan\FormBundle\AdminList;

use Kunstmaan\AdminBundle\Entity\Permission;

use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminListBundle\AdminList\Filters\StringFilter;
use Kunstmaan\AdminListBundle\AdminList\Filters\BooleanFilter;

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
     * @param AdminListFilter $builder
     *
     * @return AbstractAdminListConfigurator
     */
    public function buildFilters(AdminListFilter $builder)
    {
        $builder->add('title', new StringFilter("title"), "Title");
        $builder->add('online', new BooleanFilter("online"), "Online");

        return $this;
    }

    /**
     * @return AbstractAdminListConfigurator
     */
    public function buildFields()
    {
        $this->addField("title", "Title", true);
        $this->addField("lang", "Language", true);
        $this->addField("url", "Form path", true);

        return $this;
    }

    /**
     * @param $item
     *
     * @return array
     */
    public function getEditUrlFor($item)
    {
        return array('path' => 'KunstmaanFormBundle_formsubmissions_list', 'params' => array( 'nodetranslationid' => $item->getId()));
    }

    /**
     * @return array
     */
    public function getIndexUrlFor()
    {
        return array('path' => 'KunstmaanFormBundle_formsubmissions');
    }

    /**
     * @return bool
     */
    public function canAdd()
    {
        return false;
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function getAddUrlFor($params=array())
    {
        return "";
    }

    /**
     * @param $item
     *
     * @return bool
     */
    public function canDelete($item)
    {
        return false;
    }

    /**
     * @return string
     */
    public function getRepositoryName()
    {
        return 'KunstmaanAdminNodeBundle:NodeTranslation';
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $querybuilder
     * @param array                      $params
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
    }

    /**
     * @param $item
     *
     * @return array
     */
    public function getDeleteUrlFor($item)
    {
        return array();
    }

}

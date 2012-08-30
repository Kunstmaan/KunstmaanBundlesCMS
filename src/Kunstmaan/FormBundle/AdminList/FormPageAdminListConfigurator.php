<?php

namespace Kunstmaan\FormBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\StringFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\BooleanFilterType;
use Kunstmaan\AdminBundle\Helper\Acl\AclHelper;

/**
 * Adminlist for form pages
 */
class FormPageAdminListConfigurator extends AbstractAdminListConfigurator
{

    protected $securityContext;
    protected $permission;
    protected $aclHelper;

    /**
     * @param SecurityContextInterface $securityContext The security context
     * @param string                   $permission      The permission
     * @param AclHelper                $aclHelper       The ACL helper
     */
    public function __construct($securityContext, $permission, $aclHelper)
    {
        $this->securityContext = $securityContext;
        $this->permission      = $permission;
        $this->aclHelper       = $aclHelper;
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
        $this->addField("lang", "Language", true);
        $this->addField("url", "Form path", true);
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
    public function getIndexUrlFor()
    {
        return array('path' => 'KunstmaanFormBundle_formsubmissions');
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
                    SELECT m.id FROM Kunstmaan\FormBundle\Entity\FormSubmission s join s.node m)')
            ->addOrderBy('n.sequencenumber', 'DESC');

        $result = $this->aclHelper->apply($querybuilder, array($this->permission));

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeleteUrlFor($item)
    {
        return array();
    }

}

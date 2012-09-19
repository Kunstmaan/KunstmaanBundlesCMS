<?php

namespace Kunstmaan\FormBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\Filters\BooleanFilter;
use Kunstmaan\AdminListBundle\AdminList\Filters\StringFilter;
use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionDefinition;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;

use Doctrine\ORM\QueryBuilder;

/**
 * Adminlist for form pages
 */
class FormPageAdminListConfigurator extends AbstractAdminListConfigurator
{

    /**
     * @param string $permission The permission
     */
    public function __construct($permission)
    {
        $this->setPermissionDefinition(
            new PermissionDefinition(array($permission), 'Kunstmaan\AdminNodeBundle\Entity\Node', 'n')
        );
    }

    /**
     * @param AdminListFilter $builder
     */
    public function buildFilters(AdminListFilter $builder)
    {
        $builder->add('title', new StringFilter("title"), "Title");
        $builder->add('online', new BooleanFilter("online"), "Online");
    }

    /**
     * Build the fields
     */
    public function buildFields()
    {
        $this->addField("title", "Title", true);
        $this->addField("lang", "Language", true);
        $this->addField("url", "Form path", true);
    }

    /**
     * @param AbstractEntity $item
     *
     * @return array
     */
    public function getEditUrlFor(AbstractEntity $item)
    {
        return array(
            'path'   => 'KunstmaanFormBundle_formsubmissions_list',
            'params' => array('nodeTranslationId' => $item->getId())
        );
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
    public function getAddUrlFor(array $params = array())
    {
        return "";
    }

    /**
     * @param AbstractEntity $item
     *
     * @return bool
     */
    public function canDelete(AbstractEntity $item)
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
     * @param QueryBuilder $queryBuilder The query builder
     * @param array        $params       The parameters
     */
    public function adaptQueryBuilder(QueryBuilder $queryBuilder, array $params = array())
    {
        parent::adaptQueryBuilder($queryBuilder);
        $queryBuilder->innerJoin('b.node', 'n', 'WITH', 'b.node = n.id')
            ->andWhere(
                'n.id IN (SELECT m.id FROM Kunstmaan\FormBundle\Entity\FormSubmission s join s.node m)'
            )
            ->addOrderBy('n.sequenceNumber', 'DESC');
    }

    /**
     * @param AbstractEntity $item
     *
     * @return array
     */
    public function getDeleteUrlFor(AbstractEntity $item)
    {
        return array();
    }

}

<?php

namespace Kunstmaan\FormBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminListBundle\AdminList\Filters\StringFilter;
use Kunstmaan\AdminListBundle\AdminList\Filters\BooleanFilter;

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
    public function getAddUrlFor($params = array())
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
     * @param QueryBuilder $querybuilder
     * @param array        $params
     */
    public function adaptQueryBuilder($queryBuilder, $params = array())
    {
        parent::adaptQueryBuilder($queryBuilder);
        $queryBuilder->innerJoin('b.node', 'n', 'WITH', 'b.node = n.id')
            ->andWhere(
                'n.id IN (SELECT m.id FROM Kunstmaan\FormBundle\Entity\FormSubmission s join s.node m)'
            )
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

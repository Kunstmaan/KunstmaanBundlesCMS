<?php

namespace Kunstmaan\FormBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Doctrine\ORM\QueryBuilder;
use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminListBundle\AdminList\Filters\StringFilter;
use Kunstmaan\AdminListBundle\AdminList\Filters\DateFilter;
use Kunstmaan\AdminListBundle\AdminList\Filters\BooleanFilter;
use Kunstmaan\AdminNodeBundle\Entity\NodeTranslation;

/**
 * The form submissions admin list configurator
 */
class FormSubmissionAdminListConfigurator extends AbstractAdminListConfigurator
{

    protected $nodeTranslation;

    /**
     * @param NodeTranslation $nodeTranslation
     */
    public function __construct($nodeTranslation)
    {
        $this->nodeTranslation = $nodeTranslation;
    }

    /**
     * @param AdminListFilter $builder
     */
    public function buildFilters(AdminListFilter $builder)
    {
        $builder->add('created', new DateFilter("created"), "Date");
        $builder->add('lang', new BooleanFilter("lang"), "Language");
        $builder->add('ipAddress', new StringFilter("ipAddress"), "IP Address");
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField("created", "Date", true);
        $this->addField("lang", "Language", true);
        $this->addField("ipAddress", "ipAddress", true);
    }

    /**
     * @param AbstractEntity $item
     *
     * @return array
     */
    public function getEditUrlFor(AbstractEntity $item)
    {
        return array('path' => 'KunstmaanFormBundle_formsubmissions_list_edit', 'params' => array('nodeTranslationId' => $this->nodeTranslation->getId(), 'submissionId' => $item->getId()));
    }

    /**
     * @return array
     */
    public function getIndexUrlFor()
    {
        return array('path' => 'KunstmaanFormBundle_formsubmissions_list', 'params' => array('nodeTranslationId' => $this->nodeTranslation->getId()));
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
        return 'KunstmaanFormBundle:FormSubmission';
    }

    /**
     * @param QueryBuilder $queryBuilder The query builder
     * @param array        $params       The parameters
     */
    public function adaptQueryBuilder(QueryBuilder $queryBuilder, array $params = array())
    {
        parent::adaptQueryBuilder($queryBuilder);
        $queryBuilder
                ->innerJoin('b.node', 'n', 'WITH', 'b.node = n.id')
                ->andWhere('n.id = :node')
                ->setParameter('node', $this->nodeTranslation->getNode()->getId())
                ->addOrderBy('b.created', 'DESC');
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

    /**
     * @return array|string
     */
    public function getExportUrlFor()
    {
        return array('path' => 'KunstmaanFormBundle_formsubmissions_export', 'params' => array('nodeTranslationId' => $this->nodeTranslation->getId()));
    }

    /**
     * @return bool
     */
    public function canExport()
    {
        return true;
    }
}

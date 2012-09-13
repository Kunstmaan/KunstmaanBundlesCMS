<?php

namespace Kunstmaan\FormBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;
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
     *
     * @return AbstractAdminListConfigurator
     */
    public function buildFilters(AdminListFilter $builder)
    {
        $builder->add('created', new DateFilter("created"), "Date");
        $builder->add('lang', new BooleanFilter("lang"), "Language");
        $builder->add('ipAddress', new StringFilter("ipAddress"), "IP Address");

        return $this;
    }

    /**
     * @return FormSubmissionAdminListConfigurator
     */
    public function buildFields()
    {
        $this->addField("created", "Date", true);
        $this->addField("lang", "Language", true);
        $this->addField("ipAddress", "ipAddress", true);

        return $this;
    }

    /**
     * @param $item
     *
     * @return array
     */
    public function getEditUrlFor($item)
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
        return 'KunstmaanFormBundle:FormSubmission';
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array                      $params
     */
    public function adaptQueryBuilder(QueryBuilder $queryBuilder, $params = array())
    {
        parent::adaptQueryBuilder($queryBuilder);
        $queryBuilder
                ->innerJoin('b.node', 'n', 'WITH', 'b.node = n.id')
                ->andWhere('n.id = :node')
                ->setParameter('node', $this->nodeTranslation->getNode()->getId())
                ->addOrderBy('b.created', 'DESC');
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

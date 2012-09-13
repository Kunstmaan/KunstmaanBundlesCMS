<?php

namespace Kunstmaan\FormBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminListBundle\AdminList\Filters\StringFilter;
use Kunstmaan\AdminListBundle\AdminList\Filters\DateFilter;
use Kunstmaan\AdminListBundle\AdminList\Filters\BooleanFilter;
use Kunstmaan\AdminNodeBundle\Entity\NodeTranslation;

/**
 * The form submssions admin list configurator
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
     *
     */
    public function buildFields()
    {
        $this->addField("created", "Date", true);
        $this->addField("lang", "Language", true);
        $this->addField("ipAddress", "ipAddress", true);
    }

    /**
     * {@inheritdoc}
     */
    public function getEditUrlFor($item)
    {
        return array('path' => 'KunstmaanFormBundle_formsubmissions_list_edit', 'params' => array('nodetranslationid' => $this->nodeTranslation->getId(), 'submissionid' => $item->getId()));
    }

    /**
     * {@inheritdoc}
     */
    public function getIndexUrlFor()
    {
        return array('path' => 'KunstmaanFormBundle_formsubmissions_list', 'params' => array('nodetranslationid' => $this->nodeTranslation->getId()));
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
    public function getAddUrlFor($params = array())
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
        return 'KunstmaanFormBundle:FormSubmission';
    }

    /**
     * {@inheritdoc}
     */
    public function adaptQueryBuilder($querybuilder, $params = array())
    {
        parent::adaptQueryBuilder($querybuilder);
        $querybuilder
                ->innerJoin('b.node', 'n', 'WITH', 'b.node = n.id')
                ->andWhere('n.id = ?1')
                ->setParameter(1, $this->nodeTranslation->getNode()->getId())
                ->addOrderBy('b.created', 'DESC');
    }

    /**
     * {@inheritdoc}
     */
    public function getDeleteUrlFor($item)
    {
        return array();
    }

    public function getExportUrlFor()
    {
        return array('path' => 'KunstmaanFormBundle_formsubmissions_export', 'params' => array('nodetranslationid' => $this->nodeTranslation->getId()));
    }

    public function canExport()
    {
        return true;
    }
}

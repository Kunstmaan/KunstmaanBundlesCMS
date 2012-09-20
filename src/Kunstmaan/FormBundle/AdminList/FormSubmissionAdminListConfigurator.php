<?php

namespace Kunstmaan\FormBundle\AdminList;

use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\Filters\BooleanFilter;
use Kunstmaan\AdminListBundle\AdminList\Filters\DateFilter;
use Kunstmaan\AdminListBundle\AdminList\Filters\StringFilter;
use Kunstmaan\AdminNodeBundle\Entity\NodeTranslation;

use Doctrine\ORM\QueryBuilder;

/**
 * Adminlist configuration to list all the form submissions for a given NodeTranslation
 */
class FormSubmissionAdminListConfigurator extends AbstractAdminListConfigurator
{

    /**
     * @var NodeTranslation
     */
    protected $nodeTranslation;

    /**
     * @param NodeTranslation $nodeTranslation
     */
    public function __construct($nodeTranslation)
    {
        $this->nodeTranslation = $nodeTranslation;
    }

    /**
     * Configure the fields you can filter on
     *
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
     * Return the url to edit the given $item
     *
     * @param AbstractEntity $item
     *
     * @return array
     */
    public function getEditUrlFor(AbstractEntity $item)
    {
        return array('path' => 'KunstmaanFormBundle_formsubmissions_list_edit', 'params' => array('nodeTranslationId' => $this->nodeTranslation->getId(), 'submissionId' => $item->getId()));
    }

    /**
     * Return the url to list all the items
     *
     * @return array
     */
    public function getIndexUrlFor()
    {
        return array('path' => 'KunstmaanFormBundle_formsubmissions_list', 'params' => array('nodeTranslationId' => $this->nodeTranslation->getId()));
    }

    /**
     * Configure if it's possible to add new items
     *
     * @return bool
     */
    public function canAdd()
    {
        return false;
    }

    /**
     * Configure the types of items you can add
     *
     * @param array $params
     *
     * @return string
     */
    public function getAddUrlFor(array $params = array())
    {
        return "";
    }

    /**
     * Configure if it's possible to delete the given $item
     *
     * @param AbstractEntity $item
     *
     * @return bool
     */
    public function canDelete(AbstractEntity $item)
    {
        return false;
    }

    /**
     * Configure the repository name of the items that will be listed
     *
     * @return string
     */
    public function getRepositoryName()
    {
        return 'KunstmaanFormBundle:FormSubmission';
    }

    /**
     * Make some modifications to the default created query builder
     *
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
     * Get the delete url for the given $item
     *
     * @param AbstractEntity $item
     *
     * @return array
     */
    public function getDeleteUrlFor(AbstractEntity $item)
    {
        return array();
    }

    /**
     * Get the url to export the listed items
     *
     * @return array|string
     */
    public function getExportUrlFor()
    {
        return array('path' => 'KunstmaanFormBundle_formsubmissions_export', 'params' => array('nodeTranslationId' => $this->nodeTranslation->getId()));
    }

    /**
     * Configure if it's possible to export the listed items
     *
     * @return bool
     */
    public function canExport()
    {
        return true;
    }
}

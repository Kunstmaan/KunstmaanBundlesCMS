<?php

namespace Kunstmaan\FormBundle\AdminList;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Entity\EntityInterface;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\StringFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\DateFilterType;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Doctrine\ORM\QueryBuilder;

/**
 * Adminlist configuration to list all the form submissions for a given NodeTranslation
 */
class FormSubmissionAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{

    /**
     * @var NodeTranslation
     */
    protected $nodeTranslation;

    /**
     * @param EntityManager   $em              The entity manager
     * @param NodeTranslation $nodeTranslation The node translation
     */
    public function __construct(EntityManager $em, $nodeTranslation)
    {
        parent::__construct($em);
        $this->nodeTranslation = $nodeTranslation;
    }

    /**
     * Configure the fields you can filter on
     */
    public function buildFilters()
    {
        $builder = $this->getFilterBuilder();
        $builder->add('created', new DateFilterType("created"), "Date")
                ->add('lang', new StringFilterType("lang"), "Language")
                ->add('ipAddress', new StringFilterType("ipAddress"), "IP Address");
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField("created", "Date", true)
             ->addField("lang", "Language", true)
             ->addField("ipAddress", "ipAddress", true);
    }

    /**
     * Add a view action.
     */
    public function buildItemActions()
    {
        $nodeTranslation = $this->nodeTranslation;
        $create_route = function (EntityInterface $item) use ($nodeTranslation)  {
            $arr = array("path" => "KunstmaanFormBundle_formsubmissions_list_edit", "params" => array("nodeTranslationId" => $nodeTranslation->getId(), "submissionId" => $item->getId()));
            return $arr;
        };
        $ia = new \Kunstmaan\AdminListBundle\AdminList\ItemAction\SimpleItemAction($create_route, "eye-open", "View");
        $this->addItemAction($ia);
    }

    public function canEdit($item)
    {
        return false;
    }

    /**
     * Return the url to edit the given $item
     *
     * @param mixed $item
     *
     * @return array
     */
    public function getEditUrlFor($item)
    {
        return array(
            'path' => 'KunstmaanFormBundle_formsubmissions_list_edit',
            'params' => array('nodeTranslationId' => $this->nodeTranslation->getId(), 'submissionId' => $item->getId())
        );
    }

    /**
     * Return the url to list all the items
     *
     * @return array
     */
    public function getIndexUrl()
    {
        return array(
            'path' => 'KunstmaanFormBundle_formsubmissions_list',
            'params' => array('nodeTranslationId' => $this->nodeTranslation->getId())
        );
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
     * @param mixed $item
     *
     * @return bool
     */
    public function canDelete($item)
    {
        return false;
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

    /**
     * Get the delete url for the given $item
     *
     * @param mixed $item
     *
     * @return array
     */
    public function getDeleteUrlFor($item)
    {
        return array();
    }

    /**
     * Get the url to export the listed items
     *
     * @return array|string
     */
    public function getExportUrl()
    {
        return array('path' => 'KunstmaanFormBundle_formsubmissions_export', 'params' => array('nodeTranslationId' => $this->nodeTranslation->getId()));
    }

    /**
     * @return string
     */
    public function getBundleName()
    {
        return 'KunstmaanFormBundle';
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        return 'FormSubmission';
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
                ->andWhere('b.lang = :lang')
                ->setParameter('node', $this->nodeTranslation->getNode()->getId())
                ->setParameter('lang', $this->nodeTranslation->getLang())
                ->addOrderBy('b.created', 'DESC');
    }

}

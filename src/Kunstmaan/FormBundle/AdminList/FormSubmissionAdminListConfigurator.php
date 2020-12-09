<?php

namespace Kunstmaan\FormBundle\AdminList;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Kunstmaan\AdminBundle\Entity\EntityInterface;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\DateFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\StringFilterType;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;

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
     * @var bool
     */
    protected $deletableFormsubmissions;

    /**
     * @param EntityManager   $em                       The entity manager
     * @param NodeTranslation $nodeTranslation          The node translation
     * @param bool            $deletableFormsubmissions Can formsubmissions be deleted or not
     */
    public function __construct(EntityManager $em, $nodeTranslation, $deletableFormsubmissions = false)
    {
        parent::__construct($em);
        $this->nodeTranslation = $nodeTranslation;
        $this->deletableFormsubmissions = $deletableFormsubmissions;
    }

    /**
     * Configure the fields you can filter on
     */
    public function buildFilters()
    {
        $builder = $this->getFilterBuilder();
        $builder->add('created', new DateFilterType('created'), 'Date')
                ->add('lang', new StringFilterType('lang'), 'Language')
                ->add('ipAddress', new StringFilterType('ipAddress'), 'IP Address');
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('created', 'kuma_form.submission.list.header.created', true)
             ->addField('lang', 'kuma_form.submission.list.header.language', true)
             ->addField('ipAddress', 'kuma_form.submission.list.header.ip_address', true);
    }

    /**
     * Add a view action.
     */
    public function buildItemActions()
    {
        $nodeTranslation = $this->nodeTranslation;
        $create_route = function (EntityInterface $item) use ($nodeTranslation) {
            $arr = ['path' => 'KunstmaanFormBundle_formsubmissions_list_edit', 'params' => ['nodeTranslationId' => $nodeTranslation->getId(), 'submissionId' => $item->getId()]];

            return $arr;
        };
        $ia = new \Kunstmaan\AdminListBundle\AdminList\ItemAction\SimpleItemAction($create_route, 'eye', 'View');
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
        return [
            'path' => 'KunstmaanFormBundle_formsubmissions_list_edit',
            'params' => ['nodeTranslationId' => $this->nodeTranslation->getId(), 'submissionId' => $item->getId()],
        ];
    }

    /**
     * Return the url to list all the items
     *
     * @return array
     */
    public function getIndexUrl()
    {
        return [
            'path' => 'KunstmaanFormBundle_formsubmissions_list',
            'params' => ['nodeTranslationId' => $this->nodeTranslation->getId()],
        ];
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
     * @return string
     */
    public function getAddUrlFor(array $params = [])
    {
        return '';
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
        return $this->deletableFormsubmissions;
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
        if (!$this->deletableFormsubmissions) {
            return [];
        }

        return [
            'path' => 'KunstmaanFormBundle_formsubmissions_delete',
            'params' => ['id' => $item->getId()],
        ];
    }

    /**
     * Get the url to export the listed items
     *
     * @return array|string
     */
    public function getExportUrl()
    {
        return ['path' => 'KunstmaanFormBundle_formsubmissions_export', 'params' => ['nodeTranslationId' => $this->nodeTranslation->getId()]];
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
    public function adaptQueryBuilder(QueryBuilder $queryBuilder, array $params = [])
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

<?php

namespace Kunstmaan\TranslatorBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\StringFilterType;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\TranslatorBundle\Entity\Translation;

use Doctrine\ORM\QueryBuilder;

/**
 * TranslationAdminListConfigurator
 */
class TranslationAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{

    /**
     * @var string
     */
    protected $locale;

    protected $query;

    /**
     * Configure filters
     */
    public function buildFilters()
    {
        $this->addFilter('text', new StringFilterType('text'), 'Text');
        $this->addFilter('domain', new StringFilterType('name', 'd'), 'domain');
        $this->addFilter('keyword', new StringFilterType('keyword'), 'keyword');
        $this->addFilter('locale', new StringFilterType('locale'), 'locale');
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('domain', 'domain', true);
        $this->addField('keyword', 'keyword', true);
        $this->addField('locale', 'locale', true);
        $this->addField('text', 'Text', true);
        // $this->addField('title', 'Title', true, 'KunstmaanNodeBundle:Admin:title.html.twig')
        //     ->addField('created', 'Created At', false)
        //     ->addField('updated', 'Updated At', false)
        //     ->addField('online', 'Online', true, 'KunstmaanNodeBundle:Admin:online.html.twig');
    }

    /**
     * @param mixed $item
     *
     * @return array
     */
    public function getEditUrlFor($item)
    {
        return array(
            'path'   => 'KunstmaanTranslatorBundle_translations_edit',
            'params' => array('keyword' => $item->getKeyword(), 'locale' => $item->getLocale(), 'domain' => $item->getDomain()->getName())
        );
    }

    /**
     * @return bool
     */
    public function canAdd()
    {
        return true;
    }

    /**
     * Return if current user can delete the specified item
     *
     * @param array|object $item
     *
     * @return bool
     */
    public function canDelete($item)
    {
        return false;
    }

    /**
     * @param object $item
     *
     * @return array
     */
    public function getDeleteUrlFor($item)
    {
        return array(
            'path'   => 'KunstmaanTranslatorBundle_translations_delete',
            'params' => array('keyword' => $item->getKeyword(), 'locale' => $item->getLocale(), 'domain' => $item->getDomain()->getName())
        );
    }

    /**
     * @return string
     */
    public function getBundleName()
    {
        return 'KunstmaanTranslatorBundle';
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        return 'Translation';
    }

    /**
     * Override path convention (because settings is a virtual admin subtree)
     *
     * @param string $suffix
     *
     * @return string
     */
    public function getPathByConvention($suffix = null)
    {
        if (empty($suffix)) {
            return sprintf('%s_translations', $this->getBundleName());
        }

        return sprintf('%s_translations_%s', $this->getBundleName(), $suffix);
    }

    /**
     * Override controller path (because actions for different entities are defined in a single Settings controller).
     *
     * @return string
     */
    public function getControllerPath()
    {
        return 'KunstmaanTranslatorBundle:Index';
    }

    /**
     * @param QueryBuilder $queryBuilder The query builder
     */
    public function adaptQueryBuilder(QueryBuilder $queryBuilder)
    {
        parent::adaptQueryBuilder($queryBuilder);
        $queryBuilder->innerJoin('b.domain', 'd', 'WITH', 'd.name = b.domain');
    }

    public function getItems()
    {
        return $this->getQuery()->getResult();
    }

    /**
     * @return int
     */
    public function getCount()
    {
        $number = $this->getPreparedQueryBuilder()->select('COUNT(d)')->getQuery()->getSingleScalarResult();

        return $number;
    }

    public function getPreparedQueryBuilder()
    {
        $queryBuilder = $this->getQueryBuilder();
        $this->adaptQueryBuilder($queryBuilder);

        // Apply filters
        $filters = $this->getFilterBuilder()->getCurrentFilters();

        /* @var Filter $filter */
        foreach ($filters as $filter) {
            /* @var AbstractORMFilterType $type */
            $type = $filter->getType();
            $type->setQueryBuilder($queryBuilder);
            $filter->apply();
        }

        // Apply sorting
        if (!empty($this->orderBy)) {
            $orderBy = $this->orderBy;
            if (!strpos($orderBy, '.')) {
                $orderBy = 'b.' . $orderBy;
            }
            $queryBuilder->orderBy($orderBy, ($this->orderDirection == 'DESC' ? 'DESC' : 'ASC'));
        }

        return $queryBuilder;
    }

    public function getQuery()
    {
        if (!is_null($this->query)) {
            return $this->query;
        }

        $this->query =  $this->getPreparedQueryBuilder()->getQuery();

        return $this->query;
    }

}

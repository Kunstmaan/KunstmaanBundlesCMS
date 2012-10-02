<?php

namespace Kunstmaan\AdminListBundle\AdminList\Configurator;

use Traversable;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

use Pagerfanta\Adapter\DoctrineDBALAdapter;
use Pagerfanta\Pagerfanta;

use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL\AbstractDBALFilterType;

/**
 * An abstract admin list configurator that can be used with dbal query builder
 */
abstract class AbstractDoctrineDBALAdminListConfigurator extends AbstractAdminListConfigurator
{
    /**
     * @var Connection
     */
    private $connection = null;

    /**
     * @var QueryBuilder
     */
    private $queryBuilder = null;

    /**
     * @var Pagerfanta
     */
    private $pagerfanta = null;

    /**
     * @var string
     */
    private $countField = 'b.id';

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Return the url to edit the given $item
     *
     * @param array $item
     *
     * @return array
     */
    public function getEditUrlFor($item)
    {
        $params = array('id' => $item['id']);
        $params = array_merge($params, $this->getExtraParameters());

        return array(
            'path'   => $this->getPathByConvention($this::SUFFIX_EDIT),
            'params' => $params
        );
    }

    /**
     * Get the delete url for the given $item
     *
     * @param object $item
     *
     * @return array
     */
    public function getDeleteUrlFor($item)
    {
        $params = array('id' => $item['id']);
        $params = array_merge($params, $this->getExtraParameters());

        return array(
            'path'   => $this->getPathByConvention($this::SUFFIX_DELETE),
            'params' => $params
        );
    }

    /**
     * @return Pagerfanta
     */
    public function getPagerfanta()
    {
        if (is_null($this->pagerfanta)) {
            $adapter          = new DoctrineDBALAdapter($this->getQueryBuilder(), $this->getCountField());
            $this->pagerfanta = new Pagerfanta($adapter);
            $this->pagerfanta->setCurrentPage($this->getPage());
            $this->pagerfanta->setMaxPerPage($this->getLimit());
        }

        return $this->pagerfanta;
    }

    /**
     * @param array $params
     */
    public function adaptQueryBuilder(/** @noinspection PhpUnusedParameterInspection */ array $params = array())
    {
        $this->queryBuilder->where('1=1');
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->getPagerfanta()->getNbResults();
    }

    /**
     * @return array|mixed|Traversable
     */
    public function getItems()
    {
        return $this->getPagerfanta()->getCurrentPageResults();
    }

    /**
     * @return QueryBuilder|null
     */
    public function getQueryBuilder()
    {
        if (is_null($this->queryBuilder)) {
            $this->queryBuilder = new QueryBuilder($this->connection);
            $this->adaptQueryBuilder();

            // Apply filters
            $filters = $this->getFilterBuilder()->getCurrentFilters();
            foreach ($filters as $filter) {
                /* @var AbstractDBALFilterType $type */
                $type = $filter->getType();
                $type->setQueryBuilder($this->queryBuilder);
                $filter->apply();
            }

            // Apply sorting
            if (!empty($this->orderBy)) {
                $orderBy = $this->orderBy;
                if (!strpos($orderBy, '.')) {
                    $orderBy = 'b.' . $orderBy;
                }
                $this->queryBuilder->orderBy($orderBy, ($this->orderDirection == 'DESC' ? 'DESC' : 'ASC'));
            }
        }

        return $this->queryBuilder;
    }

    /**
     * Set count field (must include table alias!)
     *
     * @param string $countField
     *
     * @return AbstractDoctrineDBALAdminListConfigurator
     */
    public function setCountField($countField)
    {
        $this->countField = $countField;

        return $this;
    }

    /**
     * Get current count field (including table alias)
     *
     * @return string
     */
    public function getCountField()
    {
        return $this->countField;
    }
}

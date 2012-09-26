<?php
namespace Kunstmaan\AdminListBundle\AdminList;

use Pagerfanta\Adapter\DoctrineDBALAdapter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

use Pagerfanta\Pagerfanta;

abstract class AbstractDoctrineDBALAdminListConfigurator extends AbstractAdminListConfigurator
{
    /* @var Connection $connection */
    private $connection = null;

    /* @var QueryBuilder $qb */
    private $queryBuilder = null;

    /* @var Pagerfanta $pagerfanta */
    private $pagerfanta = null;

    /* @var string countField */
    private $countField = 'b.id';

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return Pagerfanta
     */
    public function getPagerfanta()
    {
        if (is_null($this->pagerfanta)) {
            $adapter = new DoctrineDBALAdapter($this->getQueryBuilder(), $this->getCountField());
            $this->pagerfanta = new Pagerfanta($adapter);
            $this->pagerfanta->setCurrentPage($this->getPage());
            $this->pagerfanta->setMaxPerPage($this->getLimit());
        }

        return $this->pagerfanta;
    }

    public function adaptQueryBuilder(array $params = array())
    {
        $this->queryBuilder->where('1=1');
    }

    public function getCount()
    {
        return $this->getPagerfanta()->getNbResults();
    }

    public function getItems()
    {
        return $this->getPagerfanta()->getCurrentPageResults();
    }

    public function getQueryBuilder()
    {
        if (is_null($this->queryBuilder)) {
            $this->queryBuilder = new QueryBuilder($this->connection);
            $this->adaptQueryBuilder($this->queryBuilder);

            // Apply filters
            $filters = $this->getAdminListFilter()->getCurrentFilters();
            foreach ($filters as $filter) {
                $filter->getType()->setQueryBuilder($this->queryBuilder);
                $filter->getType()->apply($filter->getData(), $filter->getUniqueId());
            }

            // Apply sorting
            if (!empty($this->orderBy)) {
                $orderBy = $this->orderBy;
                if (!strpos($orderBy, '.')) {
                    $orderBy = 'b.' . $orderBy;
                }
                $this->queryBuilder->orderBy($orderBy, ($this->orderDirection == 'DESC' ? 'DESC' : 'ASC'));
            }

            // Apply ACL restrictions (if applicable)
            if (!is_null($this->permissionDef) && !is_null($this->aclHelper)) {
                $this->queryBuilder = $this->aclHelper->apply($this->queryBuilder, $this->permissionDef);
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

<?php
namespace Kunstmaan\AdminListBundle\AdminList;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

use Symfony\Component\HttpFoundation\Request;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;

abstract class DoctrineORMAdminListConfigurator extends AbstractAdminListConfigurator
{
    /* @var EntityManager $em */
    private $em;

    /* @var QueryBuilder $qb */
    private $queryBuilder = null;

    /* @var Pagerfanta $pagerfanta */
    private $pagerfanta = null;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return Pagerfanta
     */
    public function getPagerfanta()
    {
        if (is_null($this->pagerfanta)) {
            $adapter = new DoctrineORMAdapter($this->getQueryBuilder());
            $this->pagerfanta = new Pagerfanta($adapter);
            $this->pagerfanta->setCurrentPage($this->getPage());
            $this->pagerfanta->setMaxPerPage($this->getLimit());
        }

        return $this->pagerfanta;
    }

    public function adaptQueryBuilder(QueryBuilder $queryBuilder)
    {
        $queryBuilder->where('1=1');
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
            $this->queryBuilder = $this->em->getRepository($this->getRepositoryName())->createQueryBuilder('b');
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

            // @todo Apply ACL restrictions here?
        }

        return $this->queryBuilder;
    }

    /**
     * @return string
     */
    abstract public function getRepositoryName();
}
<?php

namespace Kunstmaan\AdminListBundle\AdminList;

use Doctrine\DBAL\Query\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;

/**
 *
 * @todo Create a base (abstract) class with 2 descendants - one with only ORM and one with only native DBAL support?
 *       If we do this we will have to adapt the factory & filter as well...
 */
class AdminList
{

    /* @var Request $request */
    protected $request = null;

    /* @var AbstractAdminListConfigurator $configurator */
    protected $configurator = null;

    /* @var EntityManager $em */
    protected $em = null;

    /* @var int $page */
    protected $page = 1;

    /* @var AdminListFilter $adminListFilter */
    protected $adminListFilter = 1;

    /* @var string $orderBy */
    protected $orderBy = null;

    /* @var string $orderDirection */
    protected $orderDirection = null;

    protected $queryParams = array();

    protected $aclHelper = null;

    /**
     * @param AbstractAdminListConfigurator $configurator
     * @param EntityManager                 $em
     * @param array                         $queryParams
     */
    public function __construct(AbstractAdminListConfigurator $configurator, EntityManager $em, $queryParams = array())
    {
        $this->configurator = $configurator;
        $this->em           = $em;
        $adminListFilter    = new AdminListFilter();
        $this->configurator->buildFilters($adminListFilter);
        $this->configurator->buildFields();
        $this->configurator->buildActions();
        $this->adminListFilter = $adminListFilter;
        $this->queryParams     = $queryParams;
    }

    /**
     * @return PaginationBean
     */
    public function getPaginationBean()
    {
        return new PaginationBean($this->getCount($this->queryParams), $this->page, $this->configurator->getLimit());
    }

    /**
     * @return AdminListFilter
     */
    public function getAdminListFilter()
    {
        return $this->adminListFilter;
    }

    /**
     * @param Request $request
     */
    public function bindRequest(Request $request)
    {
        $this->page = $request->query->get("page");
        if (is_null($this->page)) {
            $this->page = 1;
        }
        if (!is_null($request->query->get("orderBy"))) {
            $this->orderBy = $request->query->get("orderBy");
        }
        if (!is_null($request->query->get("orderDirection"))) {
            $this->orderDirection = $request->query->get("orderDirection");
        }
        $this->adminListFilter->bindRequest($request);
    }

    /**
     * @return Field[]
     */
    public function getColumns()
    {
        return $this->configurator->getFields();
    }

    /**
     * @return Field[]
     */
    public function getExportColumns()
    {
        return $this->configurator->getExportFields();
    }

    /**
     * @param array $params
     *
     * @return int
     */
    public function getCount($params = array())
    {
        $permissionDef = $this->configurator->getPermissionDefinition();
        if (!$this->configurator->useNativeQuery()) {
            $queryBuilder = $this->em->getRepository($this->configurator->getRepositoryName())->createQueryBuilder('b');
            $queryBuilder = $queryBuilder->select("count(b.id)");
            $this->configurator->adaptQueryBuilder($queryBuilder, $params);
            $this->adminListFilter->adaptQueryBuilder($queryBuilder);
            if (!is_null($permissionDef) && !is_null($this->aclHelper)) {
                $query = $this->aclHelper->apply($queryBuilder, $permissionDef);
            } else {
                $query = $queryBuilder->getQuery();
            }

            return $query->getSingleScalarResult();
        } else {
            $queryBuilder = new \Doctrine\DBAL\Query\QueryBuilder($this->em->getConnection());
            $this->configurator->adaptNativeCountQueryBuilder($queryBuilder, $params);
            $this->adminListFilter->adaptQueryBuilder($queryBuilder);
            if (!is_null($permissionDef) && !is_null($this->aclHelper)) {
                $queryBuilder = $this->aclHelper->apply($queryBuilder, $permissionDef);
            }
            $stmt = $queryBuilder->execute();

            return $stmt->fetchColumn();
        }
    }

    /**
     * @param array $params
     *
     * @return array|null
     */
    public function getItems($params = array())
    {
        $permissionDef = $this->configurator->getPermissionDefinition();
        if (!$this->configurator->useNativeQuery()) {
            $queryBuilder = $this->em->getRepository($this->configurator->getRepositoryName())->createQueryBuilder('b');
            $queryBuilder->setFirstResult(($this->page - 1) * $this->configurator->getLimit());
            $queryBuilder->setMaxResults($this->configurator->getLimit());
            $this->configurator->adaptQueryBuilder($queryBuilder, $params);
            $this->adminListFilter->adaptQueryBuilder($queryBuilder);
            if (!is_null($this->orderBy)) {
                if (!strpos($this->orderBy, '.')) {
                    $this->orderBy = 'b.' . $this->orderBy;
                }
                $queryBuilder->orderBy($this->orderBy, ($this->orderDirection == "DESC") ? 'DESC' : "ASC");
            }
            $query = $queryBuilder->getQuery();
            if (!is_null($permissionDef) && !is_null($this->aclHelper)) {
                $query = $this->aclHelper->apply($queryBuilder, $permissionDef);
            } else {
                $query = $queryBuilder->getQuery();
            }

            return $query->getResult();
        } else {
            $queryBuilder = new \Doctrine\DBAL\Query\QueryBuilder($this->em->getConnection());
            $this->configurator->adaptNativeItemsQueryBuilder($queryBuilder, $params);
            $this->adminListFilter->adaptQueryBuilder($queryBuilder);
            if (!is_null($this->orderBy)) {
                $queryBuilder->orderBy($this->orderBy, ($this->orderDirection == "DESC") ? 'DESC' : "ASC");
            }
            $queryBuilder->setFirstResult(($this->page - 1) * $this->configurator->getLimit());
            $queryBuilder->setMaxResults($this->configurator->getLimit());
            if (!is_null($permissionDef) && !is_null($this->aclHelper)) {
                $queryBuilder = $this->aclHelper->apply($queryBuilder, $permissionDef);
            }
            $stmt = $queryBuilder->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
    }

    /**
     * @param string $columnName
     *
     * @return bool
     */
    public function hasSort($columnName)
    {
        return in_array($columnName, $this->configurator->getSortFields());
    }

    /**
     * @return bool
     */
    public function canEdit()
    {
        return $this->configurator->canEdit();
    }

    /**
     * @return bool
     */
    public function canAdd()
    {
        return $this->configurator->canAdd();
    }

    /**
     * @return array
     */
    public function getIndexUrlFor()
    {
        return $this->configurator->getIndexUrlFor();
    }

    /**
     * @param $item
     *
     * @return array
     */
    public function getEditUrlFor($item)
    {
        return $this->configurator->getEditUrlFor($item);
    }

    /**
     * @param $item
     *
     * @return array
     */
    public function getDeleteUrlFor($item)
    {
        return $this->configurator->getDeleteUrlFor($item);
    }

    /**
     * @param $params
     *
     * @return array
     */
    public function getAddUrlFor($params)
    {
        return $this->configurator->getAddUrlFor($params);
    }

    /**
     * @param $item
     *
     * @return bool
     */
    public function canDelete($item)
    {
        return $this->configurator->canDelete($item);
    }

    /**
     * @return bool
     */
    public function canExport()
    {
        return $this->configurator->canExport();
    }

    /**
     * @return string
     */
    public function getExportUrlFor()
    {
        return $this->configurator->getExportUrlFor();
    }

    /**
     * @param object|array $object
     * @param string       $attribute
     *
     * @return mixed
     */
    public function getValue($object, $attribute)
    {
        return $this->configurator->getValue($object, $attribute);
    }

    /**
     * @param object|array $object
     * @param string       $attribute
     *
     * @return string
     */
    public function getStringValue($object, $attribute)
    {
        return $this->configurator->getStringValue($object, $attribute);
    }

    /**
     * @return string
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * @return string
     */
    public function getOrderDirection()
    {
        return $this->orderDirection;
    }

    /**
     * @return array
     */
    public function getCustomActions()
    {
        return $this->configurator->getCustomActions();
    }

    /**
     * @return bool
     */
    public function hasCustomActions()
    {
        return $this->configurator->hasCustomActions();
    }

    /**
     * @return bool
     */
    public function hasListActions()
    {
        return $this->configurator->hasListActions();
    }

    /**
     * @return array
     */
    public function getListActions()
    {
        return $this->configurator->getListActions();
    }

    /**
     * @param $aclHelper
     */
    public function setAclHelper($aclHelper)
    {
        $this->aclHelper = $aclHelper;
    }

    public function getAclHelper()
    {
        return $this->aclHelper;
    }
}

<?php

namespace Kunstmaan\AdminListBundle\AdminList;

use Doctrine\DBAL\Query\QueryBuilder;

class AdminList
{

    protected $request = null;

    protected $configurator = null;

    protected $em = null;

    protected $page = 1;

    protected $adminlistfilter = 1;

    protected $orderBy = null;

    protected $orderDirection = null;

    protected $queryparams = array();

    protected $aclHelper = null;

    public function __construct(AbstractAdminListConfigurator $configurator, $em, $queryparams = array())
    {
        $this->configurator = $configurator;
        $this->em = $em;
        $adminlistfilter = new AdminListFilter();
        $this->configurator->buildFilters($adminlistfilter);
        $this->configurator->buildFields();
        $this->configurator->buildActions();
        $this->adminlistfilter = $adminlistfilter;
        $this->queryparams = $queryparams;
    }

    public function getPaginationBean()
    {
        return new PaginationBean($this->getCount($this->queryparams), $this->page, $this->configurator->getLimit());
    }

    public function getAdminListFilter()
    {
        return $this->adminlistfilter;
    }

    public function bindRequest($request)
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
        $this->adminlistfilter->bindRequest($request);
    }

    public function getColumns()
    {
        return $this->configurator->getFields();
    }

    public function getExportColumns()
    {
        return $this->configurator->getExportFields();
    }

    public function getCount($params = array())
    {
        $permissionDef = $this->configurator->getPermissionDefinition();
        if (!$this->configurator->useNativeQuery()) {
            $queryBuilder = $this->em->getRepository($this->configurator->getRepositoryName())->createQueryBuilder('b');
            $queryBuilder = $queryBuilder->select("count(b.id)");
            $this->configurator->adaptQueryBuilder($queryBuilder, $params);
            $this->adminlistfilter->adaptQueryBuilder($queryBuilder);
            if (!is_null($permissionDef) && !is_null($this->aclHelper)) {
                $query = $this->aclHelper->apply($queryBuilder, $permissionDef);
            } else {
                $query = $queryBuilder->getQuery();
            }

            return $query->getSingleScalarResult();
        } else {
            $queryBuilder = new \Doctrine\DBAL\Query\QueryBuilder($this->em->getConnection());
            $this->configurator->adaptNativeCountQueryBuilder($queryBuilder, $params);
            $this->adminlistfilter->adaptQueryBuilder($queryBuilder);
            if (!is_null($permissionDef) && !is_null($this->aclHelper)) {
                $queryBuilder = $this->aclHelper->apply($queryBuilder, $permissionDef);
            }
            $stmt = $queryBuilder->execute();

            return $stmt->fetchColumn();
        }
    }

    public function getItems($params = array())
    {
        $permissionDef = $this->configurator->getPermissionDefinition();
        if (!$this->configurator->useNativeQuery()) {
            $queryBuilder = $this->em->getRepository($this->configurator->getRepositoryName())->createQueryBuilder('b');
            $queryBuilder->setFirstResult(($this->page - 1) * $this->configurator->getLimit());
            $queryBuilder->setMaxResults($this->configurator->getLimit());
            $this->configurator->adaptQueryBuilder($queryBuilder, $params);
            $this->adminlistfilter->adaptQueryBuilder($queryBuilder);
            if (!is_null($this->orderBy)) {
                if (!strpos($this->orderBy, '.')) {
                    $this->orderBy = 'b.' . $this->orderBy;
                }
                $queryBuilder->orderBy($this->orderBy, ($this->orderDirection == "DESC") ? 'DESC' : "ASC");
            }
            if (!is_null($permissionDef) && !is_null($this->aclHelper)) {
                $query = $this->aclHelper->apply($queryBuilder, $permissionDef);
            } else {
                $query = $queryBuilder->getQuery();
            }

            return $query->getResult();
        } else {
            $queryBuilder = new \Doctrine\DBAL\Query\QueryBuilder($this->em->getConnection());
            $this->configurator->adaptNativeItemsQueryBuilder($queryBuilder, $params);
            $this->adminlistfilter->adaptQueryBuilder($queryBuilder);
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

    public function hasSort($columnName)
    {
        return in_array($columnName, $this->configurator->getSortFields());
    }

    public function canEdit()
    {
        return $this->configurator->canEdit();
    }

    public function canAdd()
    {
        return $this->configurator->canAdd();
    }

    public function getEditUrlFor($item)
    {
        return $this->configurator->getEditUrlFor($item);
    }

    public function getDeleteUrlFor($item)
    {
        return $this->configurator->getDeleteUrlFor($item);
    }

    public function getAddUrlFor($params)
    {
        return $this->configurator->getAddUrlFor($params);
    }

    public function canDelete($item)
    {
        return $this->configurator->canDelete($item);
    }

    public function canExport()
    {
        return $this->configurator->canExport();
    }

    public function getExportUrlFor()
    {
        return $this->configurator->getExportUrlFor();
    }

    public function getValue($object, $attribute)
    {
        return $this->configurator->getValue($object, $attribute);
    }

    public function getStringValue($object, $attribute)
    {
        return $this->configurator->getStringValue($object, $attribute);
    }

    public function getOrderBy()
    {
        return $this->orderBy;
    }

    public function getOrderDirection()
    {
        return $this->orderDirection;
    }

    public function getCustomActions()
    {
        return $this->configurator->getCustomActions();
    }

    public function hasCustomActions()
    {
        return $this->configurator->hasCustomActions();
    }

    public function hasListActions()
    {
        return $this->configurator->hasListActions();
    }

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

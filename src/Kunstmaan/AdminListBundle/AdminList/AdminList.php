<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kris
 * Date: 15/11/11
 * Time: 23:22
 * To change this template use File | Settings | File Templates.
 */

namespace Kunstmaan\AdminListBundle\AdminList;

class AdminList {

    protected $request = null;

    protected $configurator = null;

    protected $em = null;

    protected $page = 1;

    protected $adminlistfilter = 1;

    protected $orderBy = null;

    protected $orderDirection = null;

    function __construct(AbstractAdminListConfigurator $configurator, $em)
    {
        $this->configurator = $configurator;
        $this->em = $em;
        $adminlistfilter = new AdminListFilter();
        $this->configurator->buildFilters($adminlistfilter);
        $this->adminlistfilter = $adminlistfilter;
    }

    public function getPaginationBean(){
        return new PaginationBean($this->getCount(), $this->page, $this->configurator->getLimit());
    }

    public function getAdminListFilter(){
        return $this->adminlistfilter;
    }

    public function bindRequest($request){
        $this->request = $request;
        $todelete = $request->query->get("delete");
        if(!is_null($todelete)){
            $todeleteitem = $this->em->getRepository($this->configurator->getRepositoryName())->find($todelete);
            if(!is_null($todeleteitem)){
                $this->em->remove($todeleteitem);
                $this->em->flush();
            }
        }
        $this->page = $this->request->query->get("page");
        if(is_null($this->page)){
            $this->page = 1;
        }
        if(!is_null($this->request->query->get("orderBy"))){
            $this->orderBy = $this->request->query->get("orderBy");
        }
        if(!is_null($this->request->query->get("orderDirection"))){
            $this->orderDirection = $this->request->query->get("orderDirection");
        }
        $this->adminlistfilter->bindRequest($this->request);
    }

    public function getColumns(){
        $result = array();
        $this->configurator->configureListFields($result);
        return $result;
    }

    public function getCount(){
        $queryBuilder = $this->em->getRepository($this->configurator->getRepositoryName())->createQueryBuilder('b');
        $queryBuilder = $queryBuilder->select("count(b.id)");
        $this->configurator->adaptQueryBuilder($queryBuilder);
        $this->adminlistfilter->adaptQueryBuilder($queryBuilder);
        $query = $queryBuilder->getQuery();
        return $query->getSingleScalarResult();
    }

    public function getItems(){
        $queryBuilder = $this->em->getRepository($this->configurator->getRepositoryName())->createQueryBuilder('b');
        $queryBuilder->setFirstResult( ($this->page-1) * $this->configurator->getLimit() );
        $queryBuilder->setMaxResults( $this->configurator->getLimit() );
        $this->configurator->adaptQueryBuilder($queryBuilder);
        $this->adminlistfilter->adaptQueryBuilder($queryBuilder);
        if(!is_null($this->orderBy)){
            $queryBuilder->orderBy('b.'.$this->orderBy, ($this->orderDirection=="DESC")?'DESC':"ASC");
        }
        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }

    public function hasSort($columnName){
        return in_array($columnName, $this->configurator->getSortFields());
    }

    public function canEdit($item){
        return $this->configurator->canEdit($item);
    }

    public function getEditUrlFor($item){
        return $this->configurator->getEditUrlFor($item);
    }

    public function canDelete($item){
        return $this->configurator->canDelete($item);
    }

    public function getFrom($object, $attribute){
        return $this->configurator->getValue($object, $attribute);
    }

    public function getOrderBy(){
        return $this->orderBy;
    }

    public function getOrderDirection(){
        return $this->orderDirection;
    }
}

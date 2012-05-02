<?php

namespace Kunstmaan\AdminListBundle\AdminList;

use Doctrine\DBAL\Query\QueryBuilder;

class AdminList {

    protected $request = null;

    protected $configurator = null;

    protected $em = null;

    protected $page = 1;

    protected $adminlistfilter = 1;

    protected $orderBy = null;

    protected $orderDirection = null;
 
    protected $queryparams = array();
    
    function __construct(AbstractAdminListConfigurator $configurator, $em, $queryparams = array())
    {
        $this->configurator = $configurator;
        $this->em = $em;
        $adminlistfilter = new AdminListFilter();
        $this->configurator->buildFilters($adminlistfilter);
        $this->configurator->buildFields();
        $this->adminlistfilter = $adminlistfilter;
        $this->queryparams = $queryparams;
    }

    public function getPaginationBean(){
        return new PaginationBean($this->getCount($this->queryparams), $this->page, $this->configurator->getLimit());
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
        return $this->configurator->getFields();
    }

    public function getCount($params = array()){
        $queryBuilder = $this->em->getRepository($this->configurator->getRepositoryName())->createQueryBuilder('b');
        $queryBuilder = $queryBuilder->select("count(b.id)");
        $this->configurator->adaptQueryBuilder($queryBuilder, $params);
        $this->adminlistfilter->adaptQueryBuilder($queryBuilder);
        $query = $queryBuilder->getQuery();
        return $query->getSingleScalarResult();
    }

    public function getItems($params = array()){
        $queryBuilder = $this->em->getRepository($this->configurator->getRepositoryName())->createQueryBuilder('b');
        $queryBuilder->setFirstResult( ($this->page-1) * $this->configurator->getLimit() );
        $queryBuilder->setMaxResults( $this->configurator->getLimit() );
        $this->configurator->adaptQueryBuilder($queryBuilder, $params);
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

    public function canEdit(){
        return $this->configurator->canEdit();
    }
    
    public function canAdd(){
    	return $this->configurator->canAdd();
    }

    public function getEditUrlFor($item){
        return $this->configurator->getEditUrlFor($item);
    }
    
    public function getAddUrlFor($params){
    	return $this->configurator->getAddUrlFor($params);
    }

    public function canDelete($item){
        return $this->configurator->canDelete($item);
    }

    public function getValue($object, $attribute){
        return $this->configurator->getValue($object, $attribute);
    }
    
    public function getStringValue($object, $attribute){
        return $this->configurator->getStringValue($object, $attribute);
    }

    public function getOrderBy(){
        return $this->orderBy;
    }

    public function getOrderDirection(){
        return $this->orderDirection;
    }
}

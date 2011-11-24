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

    function __construct(AbstractAdminListConfigurator $configurator, $em)
    {
        $this->configurator = $configurator;
        $this->em = $em;
    }

    public function getPaginationBean(){
        return new PaginationBean($this->getCount(), $this->page, $this->configurator->getLimit());
    }

    public function bindRequest($request){
        $this->request = $request;
        $this->page = $this->request->query->get("page");
        if(is_null($this->page)){
            $this->page = 1;
        }
    }

    public function getColumns(){
        $result = array();
        $result = $this->configurator->configureListFields($result);
        return $result;
    }

    public function getCount(){
        $queryBuilder = $this->em->getRepository($this->configurator->getRepositoryName())->createQueryBuilder('b');
        $queryBuilder = $queryBuilder->select("count(b.id)");
        $this->configurator->adaptQueryBuilder($queryBuilder);
        $query = $queryBuilder->getQuery();
        return $query->getSingleScalarResult();
    }

    public function getItems(){
        $queryBuilder = $this->em->getRepository($this->configurator->getRepositoryName())->createQueryBuilder('b');
        $queryBuilder->setFirstResult( ($this->page-1) * $this->configurator->getLimit() );
        $queryBuilder->setMaxResults( $this->configurator->getLimit() );
        $this->configurator->adaptQueryBuilder($queryBuilder);
        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }

    public function getFrom($object, $attribute){
        return $this->configurator->getValue($object, $attribute);
    }
}

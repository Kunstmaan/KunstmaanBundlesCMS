<?php

namespace Kunstmaan\AdminListBundle\AdminList;

class Filter {
    protected $columnname = null;

    protected $filterdefinition = null;

    protected $uniqueid = null;

    protected $data = array();

    function __construct($columnname, $filterdefinition, $uniqueid) {
        $this->columnname = $columnname;
        $this->filterdefinition = $filterdefinition;
        $this->uniqueid = $uniqueid;
    }

    function bindRequest($request){
        $this->filterdefinition['type']->bindRequest($request, $this->data, $this->uniqueid);
    }

    function adaptQueryBuilder($querybuilder, &$expressions){
        $this->filterdefinition['type']->adaptQueryBuilder($querybuilder, $expressions, $this->data, $this->uniqueid);
    }

    public function getColumnname() {
        return $this->columnname;
    }

    public function getData(){
        return $this->data;
    }

    public function getType(){
        return $this->filterdefinition['type'];
    }

    public function getUniqueid(){
        return $this->uniqueid;
    }
}

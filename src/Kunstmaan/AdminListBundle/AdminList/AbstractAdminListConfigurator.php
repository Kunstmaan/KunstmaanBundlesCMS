<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kris
 * Date: 15/11/11
 * Time: 22:32
 * To change this template use File | Settings | File Templates.
 */

namespace Kunstmaan\AdminListBundle\AdminList;

abstract class AbstractAdminListConfigurator {

    public function buildFilters(AdminListFilter $builder){

    }

	function getSortFields() {
        $array = array();
        foreach($this->getFields() as $field){
        	if($field->isSortable()) $array[] = $field->getFieldname();
        }
        return $array;
    }

    private $fields;

    function getFields(){
    	return $this->fields;
    }

    abstract function buildFields();

    function addField($fieldname, $fieldheader, $sort){
    	$this->fields[] = new Field($fieldname, $fieldheader, $sort);
    }

   function configureListFields(&$array){
   		foreach($this->getFields() as $field){
   			$array[$field->getFieldheader()] = $field->getFieldname();
   		}
   }

    abstract function canEdit();

    abstract function getEditUrlFor($item);

    abstract function canAdd();

    abstract function getAddUrlFor($params=array());

    abstract function canDelete($item);

    abstract function getRepositoryName();

    function adaptQueryBuilder($querybuilder, $params=array()){

    }

    function getValue($item, $columnName){
        $methodName = "get".$columnName;
        if(method_exists($item, $methodName)){
            $result = $item->$methodName();
        } else {
            $methodName = "is".$columnName;
            if(method_exists($item, $methodName)){
                $result = $item->$methodName();
            } else {
                return "undefined function";
            }
        }
        if($result instanceof \DateTime){
            return $result->format('Y-m-d H:i:s');
        } else if($result instanceof \Doctrine\ORM\PersistentCollection) {
            $results = "";
            foreach($result as $entry) {
                $results[] = $entry->getName();
            }
            if (empty($results)) {
                return "";
            }
            return implode(', ', $results);
        } else if(is_array($result)) {
            return implode(', ', $result);
        } else {
            return $result;
        }
    }

    function getLimit(){
        return 10;
    }
}

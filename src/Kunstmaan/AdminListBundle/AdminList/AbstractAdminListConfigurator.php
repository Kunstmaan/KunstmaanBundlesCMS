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

    abstract function configureListFields($array);

    function adaptQueryBuilder($querybuilder){

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
        } else {
            return $result;
        }
    }

    function getLimit(){
        return 10;
    }
}

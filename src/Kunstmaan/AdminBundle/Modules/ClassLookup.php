<?php

namespace Kunstmaan\AdminBundle\Modules;

class ClassLookup
{
    public static function getClass($object)
    {
        return ($object instanceof \Doctrine\ORM\Proxy\Proxy) ? get_parent_class($object) : get_class($object);
    }
    
    public static function getClassName($object)
    {
    	$classname = explode('\\', ClassLookup::getClass($object));
    	return array_pop($classname);;
    }
}
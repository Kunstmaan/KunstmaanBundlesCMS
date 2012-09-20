<?php

namespace Kunstmaan\AdminBundle\Helper;

/**
 * Helper for looking up the classname, not the ORM proxy
 */
class ClassLookup
{
    /**
     * Get full class name of object (ie. class name including full namespace)
     *
     * @param mixed $object
     *
     * @return string|false
     */
    public static function getClass($object)
    {
        return ($object instanceof \Doctrine\ORM\Proxy\Proxy) ? get_parent_class($object) : get_class($object);
    }

    /**
     * Get class name of object (ie. class name without namespace)
     *
     * @param mixed $object
     *
     * @return string
     */
    public static function getClassName($object)
    {
        $className = explode('\\', ClassLookup::getClass($object));

        return array_pop($className);
    }
}

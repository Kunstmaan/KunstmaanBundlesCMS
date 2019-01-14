<?php

namespace Kunstmaan\UtilitiesBundle\Helper;

use Doctrine\ORM\Proxy\Proxy;

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
     * @return string the name of the class and if the given $object isn't a vaid Object false will be returned
     */
    public static function getClass($object)
    {
        return ($object instanceof Proxy) ? get_parent_class($object) : get_class($object);
    }

    /**
     * Get class name of object (ie. class name without namespace)
     *
     * @param string|object $reference
     *
     * @return string
     */
    public static function getClassName($reference)
    {
        $reference = is_string($reference) ? $reference : ClassLookup::getClass($reference);
        $className = explode('\\', $reference);

        return array_pop($className);
    }
}

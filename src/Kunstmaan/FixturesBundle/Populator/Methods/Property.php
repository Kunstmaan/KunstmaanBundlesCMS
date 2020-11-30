<?php

namespace Kunstmaan\FixturesBundle\Populator\Methods;

class Property implements MethodInterface
{
    public function canSet($object, $property, $value)
    {
        return property_exists($object, $property);
    }

    public function set($object, $property, $value)
    {
        $refl = new \ReflectionProperty($object, $property);
        $refl->setAccessible(true);
        $refl->setValue($object, $value);
    }
}

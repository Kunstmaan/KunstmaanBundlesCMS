<?php

namespace Kunstmaan\FixturesBundle\Populator\Methods;

class Property implements MethodInterface
{
    /**
     * {@inheritDoc}
     */
    public function canSet($object, $property, $value)
    {
        return property_exists($object, $property);
    }

    /**
     * {@inheritDoc}
     */
    public function set($object, $property, $value)
    {
        $refl = new \ReflectionProperty($object, $property);
        $refl->setAccessible(true);
        $refl->setValue($object, $value);
    }
}

<?php

namespace Kunstmaan\FixturesBundle\Populator\Methods;

interface MethodInterface
{
    /**
     * returns true if the method is able to set the property to the value on the object described by the given fixture
     *
     * @param string $property
     */
    public function canSet($object, $property, $value);

    /**
     * @param string $property
     */
    public function set($object, $property, $value);
}

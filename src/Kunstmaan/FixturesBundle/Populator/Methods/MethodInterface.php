<?php

namespace Kunstmaan\FixturesBundle\Populator\Methods;

interface MethodInterface
{
    /**
     * returns true if the method is able to set the property to the value on the object described by the given fixture
     *
     * @param mixed  $object
     * @param string $property
     * @param mixed  $value
     */
    public function canSet($object, $property, $value);

    /**
     * @param mixed  $object
     * @param string $property
     * @param mixed  $value
     */
    public function set($object, $property, $value);
}

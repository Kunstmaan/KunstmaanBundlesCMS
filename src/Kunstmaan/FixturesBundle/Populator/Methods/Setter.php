<?php
/**
 * Created by PhpStorm.
 * User: ruud
 * Date: 19/06/15
 * Time: 10:47
 */

namespace Kunstmaan\FixturesBundle\Populator\Methods;

class Setter implements MethodInterface
{
    /**
     * {@inheritdoc}
     */
    public function canSet($object, $property, $value)
    {
        return is_callable([$object, $this->setterFor($property)]);
    }

    /**
     * {@inheritdoc}
     */
    public function set($object, $property, $value)
    {
        $setter = $this->setterFor($property);
        $object->{$setter}($value);
    }

    /**
     * return the name of the setter for a given property
     *
     * @param string $property
     *
     * @return string
     */
    private function setterFor($property)
    {
        return "set{$property}";
    }
}

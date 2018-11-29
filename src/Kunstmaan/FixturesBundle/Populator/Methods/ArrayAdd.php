<?php

namespace Kunstmaan\FixturesBundle\Populator\Methods;

use Symfony\Component\Inflector\Inflector;

class ArrayAdd implements MethodInterface
{
    /**
     * {@inheritdoc}
     */
    public function canSet($object, $property, $value)
    {
        return is_array($value) && $this->findAdderMethod($object, $property) !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function set($object, $property, $value)
    {
        $method = $this->findAdderMethod($object, $property);
        foreach ($value as $val) {
            $object->{$method}($val);
        }
    }

    /**
     * finds the method used to append values to the named property
     *
     * @param mixed  $object
     * @param string $property
     *
     * @return string|null
     */
    private function findAdderMethod($object, $property)
    {
        if (is_callable([$object, $method = 'add' . $property])) {
            return $method;
        }
        if (class_exists('Symfony\Component\PropertyAccess\StringUtil') && method_exists('Symfony\Component\PropertyAccess\StringUtil', 'singularify')) {
            foreach ((array) Inflector::singularize($property) as $singularForm) {
                if (is_callable([$object, $method = 'add' . $singularForm])) {
                    return $method;
                }
            }
        }
        if (is_callable([$object, $method = 'add' . rtrim($property, 's')])) {
            return $method;
        }
        if (substr($property, -3) === 'ies' && is_callable([$object, $method = 'add' . substr($property, 0, -3) . 'y'])) {
            return $method;
        }

        return null;
    }
}

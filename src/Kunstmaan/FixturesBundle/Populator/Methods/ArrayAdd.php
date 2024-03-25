<?php

namespace Kunstmaan\FixturesBundle\Populator\Methods;

use Symfony\Component\String\Inflector\EnglishInflector;

class ArrayAdd implements MethodInterface
{
    public function canSet($object, $property, $value)
    {
        return is_array($value) && $this->findAdderMethod($object, $property) !== null;
    }

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
     * @param string $property
     */
    private function findAdderMethod($object, $property): ?string
    {
        if (is_callable([$object, $method = 'add' . $property])) {
            return $method;
        }

        $inflector = new EnglishInflector();
        foreach ($inflector->singularize($property) as $singularForm) {
            if (is_callable([$object, $method = 'add' . $singularForm])) {
                return $method;
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

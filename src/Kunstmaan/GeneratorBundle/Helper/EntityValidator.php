<?php

namespace Kunstmaan\GeneratorBundle\Helper;

/**
 * @internal
 */
final class EntityValidator
{
    /**
     * Performs basic checks in entity name.
     *
     * @param string $entity
     *
     * @throws \InvalidArgumentException
     */
    public static function validate($entity): string
    {
        $classFound = class_exists($entity);

        if (!$classFound) {
            throw new \InvalidArgumentException(sprintf('Entity "%s" was not found', $entity));
        }

        return $entity;
    }
}

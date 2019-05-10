<?php

namespace Kunstmaan\GeneratorBundle\Helper;

use Symfony\Component\HttpKernel\Kernel;

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
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public static function validate($entity)
    {
        if (Kernel::VERSION_ID >= 40000) {
            $classFound = class_exists($entity, true);

            if (!$classFound) {
                throw new \InvalidArgumentException(sprintf('Entity "%s" was not found', $entity));
            }

            return $entity;
        }

        if (!preg_match('{^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*:[a-zA-Z0-9_\x7f-\xff\\\/]+$}', $entity)) {
            throw new \InvalidArgumentException(sprintf('The entity name isn\'t valid ("%s" given, expecting something like AcmeBlogBundle:Blog/Post)', $entity));
        }

        return $entity;
    }
}

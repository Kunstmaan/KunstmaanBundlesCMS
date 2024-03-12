<?php

namespace Kunstmaan\AdminListBundle\Utils;

/**
 * @internal
 */
final class EntityDetails
{
    public static function getRootNamespace(string $classname): string
    {
        $parts = explode('Entity', $classname);

        return rtrim($parts[0], '\\');
    }

    public static function getEntityPart(string $classname): string
    {
        $parts = explode('Entity', $classname);

        return ltrim($parts[1], '\\');
    }

    public static function getEntityName(string $classname): string
    {
        $parts = explode('\\', $classname);

        return array_pop($parts);
    }
}

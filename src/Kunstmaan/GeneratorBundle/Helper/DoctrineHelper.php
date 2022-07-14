<?php

namespace Kunstmaan\GeneratorBundle\Helper;


use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;

/**
 * @internal
 */
final class DoctrineHelper
{
    public static function convertToTableName(string $className): string
    {
        $inflector = InflectorFactory::create()->build();
        return $inflector->tableize($inflector->pluralize($className));
    }
}

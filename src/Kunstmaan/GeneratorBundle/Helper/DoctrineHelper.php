<?php

namespace Kunstmaan\GeneratorBundle\Helper;

use Doctrine\Common\Inflector\Inflector;

/**
 * @internal
 */
final class DoctrineHelper
{
    public static function convertToTableName(string $className): string
    {
        return Inflector::tableize(Inflector::pluralize($className));
    }
}

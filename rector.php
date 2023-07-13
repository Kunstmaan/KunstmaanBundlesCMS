<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Php74\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Symfony\Set\SymfonyLevelSetList;
use Rector\PHPUnit\Rector\Class_\PreferPHPUnitThisCallRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->parallel();
    $rectorConfig->paths([
        __DIR__.'/src/Kunstmaan',
    ]);

    $rectorConfig->cacheDirectory('.build/rector');
    $rectorConfig->phpVersion(PhpVersion::PHP_80);
    $rectorConfig->importNames();
    $rectorConfig->importShortClasses(false);
    $rectorConfig->phpstanConfig(getcwd().'/phpstan.neon');

    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_80,
        PHPUnitSetList::PHPUNIT_91,
        PHPUnitSetList::PHPUNIT_CODE_QUALITY,
        PHPUnitSetList::PHPUNIT_EXCEPTION,
        SymfonyLevelSetList::UP_TO_SYMFONY_54,
        DoctrineSetList::DOCTRINE_ORM_213,
        DoctrineSetList::DOCTRINE_DBAL_30,
        DoctrineSetList::DOCTRINE_CODE_QUALITY,
        DoctrineSetList::DOCTRINE_COMMON_20,
        DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES,
    ]);

    $rectorConfig->skip([
        ArraySpreadInsteadOfArrayMergeRector::class,
        PreferPHPUnitThisCallRector::class,
    ]);
};

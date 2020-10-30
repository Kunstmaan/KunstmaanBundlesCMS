<?php

namespace Kunstmaan\AdminBundle\DependencyInjection\Compiler;

use Doctrine\Common\Cache\FilesystemCache;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @internal
 */
final class VersionCheckerCacheBcPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // NEXT_MAJOR: remove compiler pass when doctrine/cache dependency is removed.
        $cacheDefinition = $container->findDefinition('kunstmaan_admin.cache');
        if ($cacheDefinition->getClass() !== FilesystemCache::class || $cacheDefinition->getArgument(0) !== '%kernel.cache_dir%/fcache') {
            // The "kunstmaan_admin.cache service" is changed compared to the default definition, injected this service instead to keep BC.
            $versionChecker = $container->getDefinition('kunstmaan_admin.versionchecker');
            $versionChecker->setArgument(1, $cacheDefinition);

            $versionDataCollector = $container->getDefinition('kunstmaan_admin.datacollector.bundleversion');
            $versionDataCollector->setArgument(1, $cacheDefinition);
        }
    }
}

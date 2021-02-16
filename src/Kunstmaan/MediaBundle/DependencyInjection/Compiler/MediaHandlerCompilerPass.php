<?php

namespace Kunstmaan\MediaBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * MediaHandlerCompilerPass
 */
class MediaHandlerCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('kunstmaan_media.media_manager')) {
            $definition = $container->getDefinition('kunstmaan_media.media_manager');

            foreach ($container->findTaggedServiceIds('kunstmaan_media.media_handler') as $id => $attributes) {
                $definition->addMethodCall('addHandler', [new Reference($id)]);
            }
        }

        if ($container->hasDefinition('kunstmaan_media.icon_font_manager')) {
            $definition = $container->getDefinition('kunstmaan_media.icon_font_manager');

            foreach ($container->findTaggedServiceIds('kunstmaan_media.icon_font.loader') as $id => $attributes) {
                $definition->addMethodCall('addLoader', [new Reference($id), $id]);
            }
        }

        // Inject the tagged resolvers into our cache manager override
        if ($container->hasDefinition('Kunstmaan\MediaBundle\Helper\Imagine\CacheManager')) {
            $manager = $container->getDefinition('Kunstmaan\MediaBundle\Helper\Imagine\CacheManager');

            foreach ($container->findTaggedServiceIds('liip_imagine.cache.resolver') as $id => $tag) {
                $manager->addMethodCall('addResolver', [$tag[0]['resolver'], new Reference($id)]);
            }
        }
    }
}

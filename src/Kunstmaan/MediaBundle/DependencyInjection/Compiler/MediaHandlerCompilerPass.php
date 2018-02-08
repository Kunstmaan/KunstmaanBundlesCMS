<?php

namespace Kunstmaan\MediaBundle\DependencyInjection\Compiler;

use Kunstmaan\MediaBundle\Helper\IconFont\IconFontManager;
use Kunstmaan\MediaBundle\Helper\MediaManager;
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
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(MediaManager::class)) {
            $definition = $container->getDefinition(MediaManager::class);

            foreach ($container->findTaggedServiceIds('kunstmaan_media.media_handler') as $id => $attributes) {
                $definition->addMethodCall('addHandler', array(new Reference($id)));
            }
        }

        if ($container->hasDefinition(IconFontManager::class)) {
            $definition = $container->getDefinition(IconFontManager::class);

            foreach ($container->findTaggedServiceIds('kunstmaan_media.icon_font.loader') as $id => $attributes) {
                $definition->addMethodCall('addLoader', array(new Reference($id), $id));
            }
        }
    }
}

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
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('kunstmaan_media.media_manager')) {
            $definition = $container->getDefinition('kunstmaan_media.media_manager');

            foreach ($container->findTaggedServiceIds('kunstmaan_media.media_handler') as $id => $attributes) {
                $definition->addMethodCall('addHandler', array(new Reference($id)));
            }
        }

        if ($container->hasDefinition('kunstmaan_media.icon_font_manager')) {
            $definition = $container->getDefinition('kunstmaan_media.icon_font_manager');

            foreach ($container->findTaggedServiceIds('kunstmaan_media.icon_font.loader') as $id => $attributes) {
                $definition->addMethodCall('addLoader', array(new Reference($id), $id));
            }
        }
    }
}

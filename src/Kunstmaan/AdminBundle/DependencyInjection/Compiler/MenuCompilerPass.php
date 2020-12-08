<?php

namespace Kunstmaan\AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This compiler pass makes it possible to adapt the menu
 */
class MenuCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('kunstmaan_admin.menubuilder')) {
            return;
        }

        $definition = $container->getDefinition('kunstmaan_admin.menubuilder');

        foreach ($container->findTaggedServiceIds('kunstmaan_admin.menu.adaptor') as $id => $attributes) {
            $priority = isset($attributes[0]['priority']) ? $attributes[0]['priority'] : 0;

            $definition->addMethodCall('addAdaptMenu', [new Reference($id), $priority]);
        }
    }
}

<?php

namespace Kunstmaan\AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class MenuCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('kunstmaan_admin.menubuilder')) {
            return;
        }

        $definition = $container->getDefinition('kunstmaan_admin.menubuilder');

        foreach ($container->findTaggedServiceIds('kunstmaan_admin.menu.adaptor') as $id => $attributes) {
            $definition->addMethodCall('addAdaptMenu', array(new Reference($id)));
        }
    }
}

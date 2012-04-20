<?php

namespace Kunstmaan\AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class MenuCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('admin_menu.menubuilder')) {
            return;
        }

        $definition = $container->getDefinition('admin_menu.menubuilder');
        
        foreach ($container->findTaggedServiceIds('admin_menu.adaptor') as $id => $attributes) {
            $definition->addMethodCall('addAdaptMenu', array(new Reference($id)));
        }
    }
}
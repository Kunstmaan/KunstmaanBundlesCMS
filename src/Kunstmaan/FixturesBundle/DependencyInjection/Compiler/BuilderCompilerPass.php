<?php

namespace Kunstmaan\FixturesBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class BuilderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('kunstmaan_fixtures.builder.builder')) {
            return;
        }

        $definition = $container->getDefinition('kunstmaan_fixtures.builder.builder');
        $taggedServices = $container->findTaggedServiceIds('kunstmaan_fixtures.builder');

        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'addBuilder',
                    [new Reference($id), $attributes['alias']]
                );
            }
        }
    }
}

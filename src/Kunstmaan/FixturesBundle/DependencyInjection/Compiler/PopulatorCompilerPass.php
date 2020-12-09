<?php

namespace Kunstmaan\FixturesBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class PopulatorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('kunstmaan_fixtures.builder.builder')) {
            return;
        }

        $definition = $container->getDefinition('kunstmaan_fixtures.populator.populator');
        $taggedServices = $container->findTaggedServiceIds('kunstmaan_fixtures.populator');

        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'addPopulator',
                    [new Reference($id), $attributes['alias']]
                );
            }
        }
    }
}

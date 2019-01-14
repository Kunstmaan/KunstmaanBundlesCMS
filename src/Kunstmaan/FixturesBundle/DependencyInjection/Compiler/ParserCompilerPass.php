<?php

namespace Kunstmaan\FixturesBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ParserCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('kunstmaan_fixtures.parser.parser')) {
            return;
        }

        $definition = $container->getDefinition('kunstmaan_fixtures.parser.parser');
        $taggedServices = $container->findTaggedServiceIds('kunstmaan_fixtures.parser.property');

        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'addParser',
                    array(new Reference($id), $attributes['alias'])
                );
            }
        }

        $taggedServices = $container->findTaggedServiceIds('kunstmaan_fixtures.parser.spec');

        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'addSpecParser',
                    array(new Reference($id), $attributes['alias'])
                );
            }
        }
    }
}

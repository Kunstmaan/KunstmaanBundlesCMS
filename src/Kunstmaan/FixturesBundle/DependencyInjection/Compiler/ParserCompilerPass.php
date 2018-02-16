<?php

namespace Kunstmaan\FixturesBundle\DependencyInjection\Compiler;

use Kunstmaan\FixturesBundle\Parser\Parser;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ParserCompilerPass
 *
 * @package Kunstmaan\FixturesBundle\DependencyInjection\Compiler
 */
class ParserCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(Parser::class)) {
            return;
        }

        $definition = $container->getDefinition(Parser::class);
        $taggedServices = $container->findTaggedServiceIds('kunstmaan_fixtures.parser.property');

        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'addParser',
                    [new Reference($id), $attributes['alias']]
                );
            }
        }

        $taggedServices = $container->findTaggedServiceIds('kunstmaan_fixtures.parser.spec');

        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'addSpecParser',
                    [new Reference($id), $attributes['alias']]
                );
            }
        }

    }
}

<?php

namespace Kunstmaan\FixturesBundle\DependencyInjection\Compiler;

use Kunstmaan\FixturesBundle\Populator\Populator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class PopulatorCompilerPass
 *
 * @package Kunstmaan\FixturesBundle\DependencyInjection\Compiler
 */
class PopulatorCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(Populator::class)) {
            return;
        }

        $definition = $container->getDefinition(Populator::class);
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

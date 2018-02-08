<?php

namespace Kunstmaan\FixturesBundle\DependencyInjection\Compiler;

use Kunstmaan\FixturesBundle\Builder\BuildingSupervisor;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class BuilderCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(BuildingSupervisor::class)) {
            return;
        }

        $definition = $container->getDefinition(BuildingSupervisor::class);
        $taggedServices = $container->findTaggedServiceIds('kunstmaan_fixtures.builder');

        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'addBuilder',
                    array(new Reference($id), $attributes['alias'])
                );
            }
        }
    }
}

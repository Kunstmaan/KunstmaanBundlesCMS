<?php

namespace Kunstmaan\SearchBundle\DependencyInjection\Compiler;

use Kunstmaan\SearchBundle\Configuration\SearchConfigurationChain;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * CompilerPass class for SearchConfiguration
 *
 * Will find all services tagged "kunstmaan_search.search_configuration" and will add them to the chain with their
 * alias.
 */
class SearchConfigurationCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(SearchConfigurationChain::class)) {
            return;
        }

        $definition = $container->getDefinition(SearchConfigurationChain::class);
        $taggedServices = $container->findTaggedServiceIds('kunstmaan_search.search_configuration');

        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'addConfiguration',
                    [new Reference($id), $attributes['alias']]
                );
            }
        }
    }
}

<?php

namespace Kunstmaan\SearchBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * CompilerPass class for SearchProviders
 *
 * Will find all services tagged "kunstmaan_search.searchprovider" and will add them to the chain with their alias.
 */
class SearchProviderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('kunstmaan_search.search_provider_chain')) {
            return;
        }

        $definition = $container->getDefinition('kunstmaan_search.search_provider_chain');
        $taggedServices = $container->findTaggedServiceIds('kunstmaan_search.search_provider');

        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'addProvider',
                    [new Reference($id), $attributes['alias']]
                );
            }
        }
    }
}

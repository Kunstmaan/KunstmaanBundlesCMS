<?php

namespace Kunstmaan\SearchBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SearchProviderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('kunstmaan_search.searchprovider_chain')) {
            return;
        }

        $definition = $container->getDefinition(
            'kunstmaan_search.searchprovider_chain'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'kunstmaan_search.searchprovider'
        );
        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'addSearchProvider',
                    array(new Reference($id), $attributes["alias"])
                );
            }
        }
    }
}
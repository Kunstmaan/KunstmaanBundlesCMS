<?php

namespace Kunstmaan\NodeSearchBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class NodeSearcherCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('kunstmaan_node_search.search.service')) {
            return;
        }

        $searchers = [];
        foreach ($container->findTaggedServiceIds('kunstmaan_node_search.node_searcher') as $id => $tags) {
            $searchers[$id] = new Reference($id);
        }

        $container
            ->getDefinition('kunstmaan_node_search.search.service')
            ->replaceArgument(3, $searchers)
        ;
    }
}

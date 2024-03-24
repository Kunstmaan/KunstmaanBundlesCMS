<?php

namespace Kunstmaan\SeoBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('kunstmaan_seo');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('request_cache')
                    ->defaultValue('cache.app')
                    ->info('Provide a psr-6 cache service to cache all external http calls for seo images.')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

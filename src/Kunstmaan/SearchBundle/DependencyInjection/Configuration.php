<?php

namespace Kunstmaan\SearchBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('kunstmaan_search');
        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('kunstmaan_search');
        }

        $rootNode
            ->children()
                ->arrayNode('connection')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('driver')
                            ->defaultValue('elastic_search')
                            ->validate()
                                ->ifNotInArray(['elastic_search'])
                                ->thenInvalid('Invalid search driver %s')
                            ->end()
                        ->end()
                        ->scalarNode('host')->defaultNull()->end() //NEXT_MAJOR: Make config required or define default value (localhost)
                        ->integerNode('port')->defaultNull()->end() //NEXT_MAJOR: Make config required or define default value (9200)
                        ->scalarNode('username')->defaultNull()->end()
                        ->scalarNode('password')->defaultNull()->end()
                    ->end()
                ->end()
                ->scalarNode('index_prefix')->defaultNull()->end()
                ->arrayNode('analyzer_languages')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('analyzer')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}

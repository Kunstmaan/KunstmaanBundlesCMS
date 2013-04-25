<?php

namespace Kunstmaan\SearchBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kunstmaan_search');

        $rootNode
            ->children()
                ->arrayNode('analyzer_languages')
                    ->useAttributeAsKey('lang')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('lang')->end()
                            ->scalarNode('analyzer')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();


        return $treeBuilder;
    }
}

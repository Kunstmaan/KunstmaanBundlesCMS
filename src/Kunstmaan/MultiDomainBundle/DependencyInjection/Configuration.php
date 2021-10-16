<?php

namespace Kunstmaan\MultiDomainBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('kunstmaan_multi_domain');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('hosts')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                     ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('host')
                                ->isRequired()
                            ->end()
                            ->scalarNode('protocol')
                                ->defaultValue('http')
                            ->end()
                            ->arrayNode('aliases')
                                ->prototype('scalar')->end()
                            ->end()
                            ->scalarNode('type')
                                ->defaultValue('single_lang')
                            ->end()
                            ->scalarNode('root')
                                ->defaultValue('homepage')
                            ->end()
                            ->variableNode('extra')
                            ->end()
                            ->scalarNode('default_locale')
                                ->isRequired()
                            ->end()
                            ->arrayNode('locales')
                                ->isRequired()
                                ->requiresAtLeastOneElement()
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('uri_locale')->isRequired()->end()
                                        ->scalarNode('locale')->isRequired()->end()
                                        ->variableNode('extra')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}

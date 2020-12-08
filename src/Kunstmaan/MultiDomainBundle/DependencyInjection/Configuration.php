<?php

namespace Kunstmaan\MultiDomainBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('kunstmaan_multi_domain');
        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('kunstmaan_multi_domain');
        }

        $rootNode
            ->children()
                ->arrayNode('hosts')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    // ->useAttributeAsKey('host')
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

<?php

namespace Kunstmaan\MultiDomainBundle\DependencyInjection;

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
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kunstmaan_multi_domain');

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

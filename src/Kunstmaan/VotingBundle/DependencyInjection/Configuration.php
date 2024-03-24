<?php

namespace Kunstmaan\VotingBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('kunstmaan_voting');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('voting_default_value')->defaultValue(1)->end()
                ->arrayNode('actions')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->fixXmlConfig('action', 'actions')
                        ->children()
                            ->scalarNode('default_value')
                                ->defaultValue(1)
                                ->validate()
                                    ->ifTrue(function ($v) {
                                        return !is_numeric($v);
                                    })
                                    ->thenInvalid('Invalid action default value, should be a number.')
                                ->end()
                            ->end()
                            ->scalarNode('max_number_by_ip')->defaultValue(null)->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}

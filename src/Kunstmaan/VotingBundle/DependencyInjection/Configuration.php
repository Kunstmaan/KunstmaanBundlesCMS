<?php

namespace Kunstmaan\VotingBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('kunstmaan_voting');
        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('kunstmaan_voting');
        }

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

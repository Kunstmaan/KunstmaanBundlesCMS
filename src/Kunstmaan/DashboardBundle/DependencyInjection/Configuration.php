<?php

namespace Kunstmaan\DashboardBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('kunstmaan_dashboard');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('google_analytics')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('api')->info('More info at https://kunstmaanbundlescms.readthedocs.io/en/latest/cookbook/google-analytics-dashboard/')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('client_id')->defaultNull()->end()
                                ->scalarNode('client_secret')->defaultNull()->end()
                                ->scalarNode('dev_key')->defaultNull()->end()
                                ->scalarNode('app_name')->defaultValue('Kuma Analytics Dashboard')->end()
                            ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

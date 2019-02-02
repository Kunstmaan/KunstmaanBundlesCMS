<?php

namespace Kunstmaan\DashboardBundle\DependencyInjection;

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
        $treeBuilder = new TreeBuilder('kunstmaan_dashboard');
        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('kunstmaan_dashboard');
        }

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
                                ->scalarNode('app_name')->defaultNull()->end() // NEXT_MAJOR: Set default value to "Kuma Analytics Dashboard"
                            ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

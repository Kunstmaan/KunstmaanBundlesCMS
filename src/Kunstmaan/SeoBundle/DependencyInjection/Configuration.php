<?php

namespace Kunstmaan\SeoBundle\DependencyInjection;

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
        $treeBuilder = new TreeBuilder('kunstmaan_seo');
        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('kunstmaan_seo');
        }

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

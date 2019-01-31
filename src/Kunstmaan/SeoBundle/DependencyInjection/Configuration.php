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
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('kunstmaan_seo');

        $root
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

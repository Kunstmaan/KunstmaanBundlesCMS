<?php

namespace Kunstmaan\AdminBundle\DependencyInjection;

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
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kuma_admin');

        $rootNode
            ->fixXmlConfig('admin_locale')
            ->children()
                ->scalarNode('dashboard_route')->end()

                ->arrayNode('admin_locales')
                    ->defaultValue(array('en'))
                    ->prototype('scalar')->end()
                ->end()

                ->scalarNode('default_admin_locale')->cannotBeEmpty()->defaultValue('en')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

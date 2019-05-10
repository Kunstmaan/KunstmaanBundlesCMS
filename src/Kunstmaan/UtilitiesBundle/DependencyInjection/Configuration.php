<?php

namespace Kunstmaan\UtilitiesBundle\DependencyInjection;

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
        $treeBuilder = new TreeBuilder('kunstmaan_utilities');
        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('kunstmaan_utilities');
        }

        $rootNode
            ->children()
                ->arrayNode('cipher')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('secret')->defaultValue('%kernel.secret%')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

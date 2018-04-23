<?php

namespace Kunstmaan\UtilitiesBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
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
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('kunstmaan_utilities');

        /** @var ArrayNodeDefinition $pages */
        $root
            ->children()
                ->arrayNode('cipher')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('secret')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->defaultValue('%secret%')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}

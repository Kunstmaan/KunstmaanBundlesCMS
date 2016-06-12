<?php

namespace Kunstmaan\NodeBundle\DependencyInjection;

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
        $root = $treeBuilder->root('kunstmaan_node');

        /** @var ArrayNodeDefinition $pages */
        $root
            ->children()
                ->arrayNode('pages')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('name')->isRequired()->end()
                            ->scalarNode('search_type')->end()
                            ->booleanNode('structure_node')->end()
                            ->booleanNode('indexable')->end()
                            ->scalarNode('icon')->defaultNull()->end()
                            ->scalarNode('hidden_from_tree')->end()
                            ->booleanNode('is_homepage')->defaultFalse()->end()
                            ->arrayNode('allowed_children')
                                ->prototype('array')
                                    ->beforeNormalization()
                                        ->ifString()->then(function ($v) { return ["class" => $v]; })
                                    ->end()
                                    ->children()
                                        ->scalarNode('class')->isRequired()->end()
                                        ->scalarNode('name')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('publish_later_stepping')->defaultValue('15')->end()
                ->scalarNode('unpublish_later_stepping')->defaultValue('15')->end()
                ->booleanNode('show_add_homepage')->defaultTrue()->end()
            ->end();

        return $treeBuilder;
    }
}

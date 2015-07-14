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
        $pages = $root->children()->arrayNode('pages')->prototype('array');
        $pages->children()->scalarNode('name')->isRequired();
        $pages->children()->scalarNode('search_type');
        $pages->children()->booleanNode('structure_node');
        $pages->children()->booleanNode('indexable');
        $pages->children()->scalarNode('icon')->defaultNull();
        $pages->children()->scalarNode('hidden_from_tree');

        /** @var ArrayNodeDefinition $children */
        $children = $pages->children()->arrayNode('allowed_children')->prototype('array');
        $children->beforeNormalization()->ifString()->then(function ($v) { return ["class" => $v]; });
        $children->children()->scalarNode('class')->isRequired();
        $children->children()->scalarNode('name');

        return $treeBuilder;
    }
}

<?php

namespace Kunstmaan\PagePartBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
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
        $root = $treeBuilder->root('kunstmaan_page_part');
        $root->children()
            ->booleanNode('extended_pagepart_chooser')
                ->defaultFalse()
            ->end();

        /** @var ArrayNodeDefinition $pageparts */
        $pageparts = $root->children()->arrayNode('pageparts')->prototype('array');
        $pageparts->children()->scalarNode('name')->isRequired();
        $pageparts->children()->scalarNode('context')->isRequired();
        $pageparts->children()->scalarNode('extends');
        $pageparts->children()->scalarNode('widget_template');

        /** @var ArrayNodeDefinition $types */
        $types = $pageparts->children()->arrayNode('types')->defaultValue([])->prototype('array');
        $types->children()->scalarNode('name')->isRequired();
        $types->children()->scalarNode('class')->isRequired();
        $types->children()->scalarNode('preview');
        $types->children()->scalarNode('pagelimit');

        // *************************************************************************************************************

        /** @var ArrayNodeDefinition $pagetemplates */
        $pagetemplates = $root->children()->arrayNode('pagetemplates')->defaultValue([])->prototype('array');

        $pagetemplates->children()->scalarNode('template')->isRequired();
        $pagetemplates->children()->scalarNode('name')->isRequired();

        /** @var ArrayNodeDefinition $rows */
        $rows = $pagetemplates->children()->arrayNode('rows')->prototype('array');

        /** @var ArrayNodeDefinition $regions */
        $regions = $rows->children()->arrayNode('regions')->prototype('array');

        // no subregions this way, sorry. feel free to implement it: https://gist.github.com/Lumbendil/3249173
        $regions
            ->children()
                ->scalarNode('name')->end()
                ->scalarNode('span')->defaultValue(12)->end()
                ->scalarNode('template')->end()
                ->variableNode('rows')
                    ->validate()->ifTrue(function ($element) {
                        return !is_array($element);
                    })->thenInvalid('The rows element must be an array.')->end()
                    ->validate()->always(function ($children) {
                        array_walk($children, array($this, 'evaluateRows'));

                        return $children;
                    })->end()
                ->end()
            ->end();

        return $treeBuilder;
    }

    protected function evaluateRows(&$child, $name)
    {
        $child = $this->getRowNode($name)->finalize($child);
    }

    protected function getRowNode($name = '')
    {
        $treeBuilder = new TreeBuilder();
        $definition = $treeBuilder->root($name);
        $this->buildRowNode($definition);

        return $definition->getNode(true);
    }

    protected function buildRowNode(NodeDefinition $node)
    {
        return $node
                ->validate()->always(function ($children) {
                    array_walk($children, array($this, 'evaluateRegions'));

                    return $children;
                })
            ->end();
    }

    protected function evaluateRegions(&$child, $name)
    {
        $child = $this->getRegionNode($name)->finalize($child);
    }

    protected function getRegionNode($name = '')
    {
        $treeBuilder = new TreeBuilder();
        $definition = $treeBuilder->root($name);
        $this->buildRegionNode($definition);

        return $definition->getNode(true);
    }

    protected function buildRegionNode(NodeDefinition $node)
    {
        return $node
            ->children()
                ->scalarNode('name')->isRequired()->end()
                ->scalarNode('span')->defaultValue(12)->end()
                ->variableNode('rows')
                    ->validate()->ifTrue(function ($element) {
                        return !is_array($element);
                    })->thenInvalid('The rows element must be an array.')->end()
                    ->validate()->always(function ($children) {
                        array_walk($children, array($this, 'evaluateRows'));

                        return $children;
                    })->end()
                ->end()
            ->end();
    }
}

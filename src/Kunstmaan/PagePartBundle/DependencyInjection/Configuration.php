<?php

namespace Kunstmaan\PagePartBundle\DependencyInjection;

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
        $root = $treeBuilder->root('kunstmaan_page_part');

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
        $regions->children()->scalarNode('name');
        $regions->children()->scalarNode('span')->defaultValue(12);
        $regions->children()->scalarNode('template');

        return $treeBuilder;
    }
}

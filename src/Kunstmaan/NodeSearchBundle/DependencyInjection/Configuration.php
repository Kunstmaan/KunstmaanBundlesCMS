<?php

namespace Kunstmaan\NodeSearchBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('kunstmaan_node_search');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode->children()->booleanNode('enable_update_listener')->defaultTrue();
        $rootNode->children()->booleanNode('use_match_query_for_title')->defaultFalse();

        /** @var ArrayNodeDefinition $properties */
        $properties = $rootNode->children()->arrayNode('mapping')->useAttributeAsKey('name')->prototype('array');

        $types = [
            'token_count', 'text', 'keyword',
            'float', 'double', 'byte', 'short', 'integer', 'long',
            'date',
            'boolean',
            'binary',
            'geo_point',
        ];

        $properties->children()->scalarNode('type')->beforeNormalization()->ifNotInArray($types)->thenInvalid('type must be one of: ' . implode(', ', $types));

        $properties->children()->booleanNode('fielddata');
        $properties->children()->booleanNode('doc_values');
        $properties->children()
            ->scalarNode('index')
            ->beforeNormalization()
            ->ifNotInArray(['true', 'false', true, false])
            ->thenInvalid('index must be one of: true, false');

        $properties->children()->booleanNode('store');
        $properties->children()->floatNode('boost');
        $properties->children()->scalarNode('null_value');
        $properties->children()->scalarNode('analyzer');
        $properties->children()->scalarNode('search_analyzer');
        $properties->children()->scalarNode('index_analyzer');
        $properties->children()->scalarNode('copy_to');
        $properties->children()->scalarNode('term_vector')->beforeNormalization()->ifNotInArray(['yes', 'no', 'with_positions', 'with_offsets', 'with_positions_offsets'])
            ->thenInvalid('term_vector must be one of: yes, no, with_positions, with_offsets, with_positions_offsets');

        $rootNode
            ->children()
                ->arrayNode('contexts')
                ->defaultValue([])
                ->prototype('scalar')->end()
            ->end();

        return $treeBuilder;
    }
}

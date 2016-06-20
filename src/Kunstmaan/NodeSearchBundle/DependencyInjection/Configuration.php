<?php

namespace Kunstmaan\NodeSearchBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link
 * http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('kunstmaan_node_search');

        $rootNode->children()->booleanNode('enable_update_listener')->defaultTrue();
        $rootNode->children()->booleanNode('use_match_query_for_title')->defaultFalse();

        /** @var ArrayNodeDefinition $properties */
        $properties = $rootNode->children()->arrayNode('mapping')->useAttributeAsKey('name')->prototype('array');

        $properties->children()->scalarNode('type')->beforeNormalization()->ifNotInArray($types = [
            'string', 'token_count',
            'float', 'double', 'byte', 'short', 'integer', 'long',
            'date',
            'boolean',
            'binary',
        ])->thenInvalid('type must be one of: ' . implode(', ', $types));
        $properties->children()->scalarNode('index')->beforeNormalization()->ifNotInArray(['analyzed', 'not_analyzed', 'no'])
            ->thenInvalid("index must be one of: analyzed, not_analyzed, no");
        $properties->children()->booleanNode('include_in_all');
        $properties->children()->booleanNode('store');
        $properties->children()->floatNode('boost');
        $properties->children()->scalarNode('null_value');
        $properties->children()->scalarNode('analyzer');
        $properties->children()->scalarNode('index_analyzer');
        $properties->children()->scalarNode('copy_to');

        return $treeBuilder;
    }
}

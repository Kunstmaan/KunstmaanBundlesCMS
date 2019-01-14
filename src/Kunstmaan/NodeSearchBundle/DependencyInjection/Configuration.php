<?php

namespace Kunstmaan\NodeSearchBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link * http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @var bool
     */
    private $useElasticSearchVersion6;

    /**
     * Configuration constructor.
     *
     * @param bool $useElasticSearchVersion6
     */
    public function __construct($useElasticSearchVersion6)
    {
        $this->useElasticSearchVersion6 = $useElasticSearchVersion6;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kunstmaan_node_search');

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
        ];
        if (!$this->useElasticSearchVersion6) {
            $types[] = 'string';
        }

        $properties->children()->scalarNode('type')->beforeNormalization()->ifNotInArray($types)->thenInvalid('type must be one of: ' . implode(', ', $types));

        if ($this->useElasticSearchVersion6) {
            $properties->children()->booleanNode('fielddata');
            $properties->children()->booleanNode('doc_values');
            $properties->children()
                ->scalarNode('index')
                ->beforeNormalization()
                ->ifNotInArray(['true', 'false', true, false])
                ->thenInvalid('index must be one of: true, false');
        } else {
            $properties->children()
                ->scalarNode('index')
                ->beforeNormalization()
                ->ifNotInArray(['analyzed', 'not_analyzed', 'no'])
                ->thenInvalid('index must be one of: analyzed, not_analyzed, no');
            $properties->children()->booleanNode('include_in_all');
        }

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

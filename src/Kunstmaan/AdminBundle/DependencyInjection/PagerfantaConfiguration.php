<?php

namespace Kunstmaan\AdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Container configuration to bridge the configuration from WhiteOctoberPagerfantaBundle to BabDevPagerfantaBundle
 * NEXT_MAJOR remove class
 *
 * @deprecated since KunstmaanAdminBundle 5.9. Migrate your Pagerfanta configuration from WhiteOctoberPagerfantaBundle to BabDevPagerfantaBundle, the configuration bridge will be removed in KunstmaanAdminBundle 6.0.
 *
 * @internal
 */
final class PagerfantaConfiguration implements ConfigurationInterface
{
    public const EXCEPTION_STRATEGY_TO_HTTP_NOT_FOUND = 'to_http_not_found';

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('white_october_pagerfanta');
        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('white_october_pagerfanta');
        }

        $this->addExceptionsStrategySection($rootNode);

        $rootNode
            ->children()
                ->scalarNode('default_view')
                    ->defaultValue('default')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    private function addExceptionsStrategySection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('exceptions_strategy')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('out_of_range_page')->defaultValue(self::EXCEPTION_STRATEGY_TO_HTTP_NOT_FOUND)->end()
                        ->scalarNode('not_valid_current_page')->defaultValue(self::EXCEPTION_STRATEGY_TO_HTTP_NOT_FOUND)->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}

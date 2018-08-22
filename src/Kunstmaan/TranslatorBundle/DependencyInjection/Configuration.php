<?php

namespace Kunstmaan\TranslatorBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('kuma_translator');

        $availableStorageEngines = array('orm');
        $defaultFileFormats = array('yml', 'xliff');

        $rootNode
            ->children()
                ->booleanNode('enabled')
                    ->defaultTrue()
                ->end()

                ->scalarNode('default_bundle')
                    ->cannotBeEmpty()
                    ->defaultValue('own')
                ->end()

                ->arrayNode('bundles')
                    ->defaultValue(array())
                    ->prototype('scalar')->end()
                ->end()

                ->scalarNode('cache_dir')
                    ->cannotBeEmpty()
                    ->defaultValue("%kernel.cache_dir%/translations")
                ->end()

                ->booleanNode('debug')
                    ->defaultValue(null)
                ->end()

                ->arrayNode('managed_locales')
                    ->defaultValue(array())
                    ->prototype('scalar')->end()
                ->end()

                ->arrayNode('file_formats')
                    ->defaultValue($defaultFileFormats)
                    ->prototype('scalar')->end()
                ->end()

                ->arrayNode('storage_engine')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('type')
                            ->cannotBeEmpty()
                            ->defaultValue('orm')
                            ->validate()
                                ->ifNotInArray($availableStorageEngines)
                                ->thenInvalid('Storage engine should be one of the following: '.implode(', ', $availableStorageEngines))
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

<?php

namespace Kunstmaan\MediaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    private const DEFAULT_CROPPING_VIEWS_CONFIG = [
        ['name' => 'desktop', 'width' => 1, 'height' => 1, 'lock_ratio' => true],
        ['name' => 'tablet', 'width' => 1, 'height' => 1, 'lock_ratio' => true],
        ['name' => 'phone', 'width' => 1, 'height' => 1, 'lock_ratio' => true],
    ];
    private const DEFAULT_FOCUS_POINT_CLASSES = ['top-left', 'top-center', 'top-right', 'center-left', 'center', 'center-right', 'bottom-left', 'bottom-center', 'bottom-right'];

    /**
     * Generates the configuration tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('kunstmaan_media');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('soundcloud_api_key')->defaultValue('YOUR_CLIENT_ID')->end()
                ->arrayNode('remote_video')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('vimeo')->defaultTrue()->end()
                        ->booleanNode('youtube')->defaultTrue()->end()
                        ->booleanNode('dailymotion')->defaultTrue()->end()
                    ->end()
                ->end()
                ->booleanNode('enable_pdf_preview')->defaultFalse()->end()
                ->arrayNode('blacklisted_extensions')
                    ->defaultValue(['php', 'htaccess'])
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('web_root')
                    ->defaultValue('%kernel.project_dir%/public')
                    ->cannotBeEmpty()
                ->end()
                // @experimental This feature is experimental and is a subject to change, be advised when using this feature and classes.
                ->arrayNode('cropping_views')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('default')
                            ->defaultValue(self::DEFAULT_CROPPING_VIEWS_CONFIG)
                            ->arrayPrototype()
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('name')->end()
                                    ->integerNode('width')->end()
                                    ->integerNode('height')->end()
                                    ->booleanNode('lock_ratio')->defaultTrue()->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('focus_point_classes')
                            ->defaultValue(self::DEFAULT_FOCUS_POINT_CLASSES)
                            ->prototype('array')->end()
                        ->end()
                        ->arrayNode('custom_views')
                            ->useAttributeAsKey('groupName')
                            ->arrayPrototype()
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->booleanNode('use_focus_point')->defaultFalse()->end()
                                    ->booleanNode('use_cropping')->defaultTrue()->end()
                                    ->arrayNode('views')
                                        ->arrayPrototype()
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('name')->end()
                                                ->integerNode('width')->end()
                                                ->integerNode('height')->end()
                                                ->booleanNode('lock_ratio')->defaultTrue()->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}

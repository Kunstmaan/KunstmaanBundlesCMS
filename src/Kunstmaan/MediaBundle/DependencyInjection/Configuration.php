<?php

namespace Kunstmaan\KMediaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kunstmaan_k_media');

        $this->addCdnSection($rootNode);
        $this->addProviderSection($rootNode);
        $this->addGeneratorSection($rootNode);
        $this->addManipulatorSection($rootNode);
        $this->addFilesystemSection($rootNode);
        $this->addContextsSection($rootNode);

        return $treeBuilder;
    }

    /**
     * Parses the ano_media.cdn config section
     * Example for yaml driver:
     * ano_media:
     *     cdn: ano_media.cdn.remote_server
     *
     * @param ArrayNodeDefinition $node
     * @return void
     */
    private function addCdnSection(ArrayNodeDefinition $node)
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('cdn')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->beforeNormalization()
                            ->ifTrue(function($v) { return !is_array($v); })
                            ->thenInvalid('The kunstmaan_k_media.cdn config "%s" must be an array.')
                        ->end()
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('default')->defaultValue(false)->end()
                            ->scalarNode('id')->isRequired()->end()
                            ->arrayNode('options')
                                ->useAttributeAsKey('name')
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                    ->defaultValue(array(
                        'local' => array(
                            'default' => true,
                            'id' => 'kunstmaan_k_media.cdn.remote_server',
                            'options' => array(
                                'base_url' => '/media'
                            )
                        )
                    ))
                ->end()
            ->end();
    }

    /**
     * Parses the ano_media.provider config section
     * Example for yaml driver:
     * ano_media:
     *     provider:
     *         image:
     *             default: true #optional
     *             id: ano_media.provider.image
     *             cdn: local #optional
     *
     * @param ArrayNodeDefinition $node
     * @return void
     */
    private function addProviderSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('provider')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->beforeNormalization()
                            ->ifTrue(function($v) { return !is_array($v); })
                            ->thenInvalid('The kunstmaan_k_media.provider config "%s" must be an array.')
                        ->end()
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('default')->defaultValue(false)->end()
                            ->scalarNode('id')->isRequired()->end()
                            ->scalarNode('cdn')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * Parses the ano_media.generator config section
     * Example for yaml driver:
     * ano_media:
     *     generator:
     *         path:
     *             default:
     *                 default: true
     *                 id: ano_media.generator.path.default
     *         uuid:
     *             default:
     *                 default: true
     *                 id: ano_media.generator.uuid.default
     *
     * @param ArrayNodeDefinition $node
     * @return void
     */
    private function addGeneratorSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('generator')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('path')
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->beforeNormalization()
                                    ->ifTrue(function($v) { return !is_array($v); })
                                    ->thenInvalid('The kunstmaan_k_media.generator.path config "%s" must be an array.')
                                ->end()
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('default')->defaultValue(false)->end()
                                    ->scalarNode('id')->isRequired()->end()
                                ->end()
                            ->end()
                            ->defaultValue(array(
                                'default' => array(
                                    'default' => true,
                                    'id' => 'kunstmaan_k_media.generator.path.default',
                                )
                            ))
                        ->end()
                        ->arrayNode('uuid')
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->beforeNormalization()
                                    ->ifTrue(function($v) { return !is_array($v); })
                                    ->thenInvalid('The kunstmaan_k_media.generator.uuid config "%s" must be an array.')
                                ->end()
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('default')->defaultValue(false)->end()
                                    ->scalarNode('id')->isRequired()->end()
                                ->end()
                            ->end()
                            ->defaultValue(array(
                                'default' => array(
                                    'default' => true,
                                    'id' => 'kunstmaan_k_media.generator.uuid.default',
                                )
                            ))
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * Parses the ano_media.manipulator config section
     * Example for yaml driver:
     * ano_media:
     *     manipulator:
     *         image:
     *             default: true
     *             id: ano_media.util.image.manipulator.imagine
     *
     * @param ArrayNodeDefinition $node
     * @return void
     */
    private function addManipulatorSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('manipulator')
                    ->addDefaultsIfNotSet()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->beforeNormalization()
                            ->ifTrue(function($v) { return !is_array($v); })
                            ->thenInvalid('The kunstmaan_k_media.manipulator config "%s" must be an array.')
                        ->end()
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('default')->defaultValue(false)->end()
                            ->scalarNode('id')->isRequired()->end()
                        ->end()
                    ->end()
                    ->defaultValue(array(
                        'imagine' => array(
                            'default' => true,
                            'id' => 'kunstmaan_k_media.util.image.manipulator.imagine',
                        )
                    ))
                ->end()
            ->end();
    }

    /**
     * Parses the ano_media.filesystem config section
     * Example for yaml driver:
     * ano_media:
     *     filesystem:
     *         local:
     *             default: true
     *             id: ano_media.filesystem.local
     *             options:
     *                 base_path: %kernel.root_dir%/../web/media
     *                 create: true
     *
     * @param ArrayNodeDefinition $node
     * @return void
     */
    private function addFilesystemSection(ArrayNodeDefinition $node)
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('filesystem')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->beforeNormalization()
                            ->ifTrue(function($v) { return !is_array($v); })
                            ->thenInvalid('The kunstmaan_k_media.filesystem config "%s" must be an array.')
                        ->end()
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('default')->defaultValue(false)->end()
                            ->scalarNode('id')->isRequired()->end()
                            ->arrayNode('options')
                                ->useAttributeAsKey('name')
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                    ->defaultValue(array(
                        'local' => array(
                            'default' => true,
                            'id' => 'kunstmaan_k_media.filesystem.local',
                            'options' => array(
                                'base_path' => '%kernel.root_dir%/../web/media',
                                'create' => true
                            )
                        )
                    ))
                ->end()
            ->end();
    }

    /**
     * Parses the ano_media.contexts config section
     * Example for yaml driver:
     * ano_media:
     *     contexts:
     *         user_picture:
     *             provider: ano_media.provider.image
     *             generator:
     *                 path: ano_media.generator.path.default
     *                 uuid: ano_media.generator.uuid.default
     *             formats:
     *                 small: { width: 50, height: 50 }
     *                 medium: { width: 90, height: 90 }
     *                 large:  { width: 200, height: 200 }
     *
     * @param ArrayNodeDefinition $node
     * @return void
     */
    private function addContextsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('contexts')
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->beforeNormalization()
                            ->ifTrue(function($v) { return !is_array($v); })
                            ->thenInvalid('The kunstmaan_k_media.contexts config "%s" must be an array.')
                        ->end()
                        ->children()
                            ->scalarNode('provider')->end()
                            ->arrayNode('generator')
                                ->children()
                                    ->scalarNode('path')->end()
                                    ->scalarNode('uuid')->end()
                                ->end()
                            ->end()
                            ->arrayNode('formats')
                                ->useAttributeAsKey('name')
                                ->prototype('array')
                                    ->useAttributeAsKey('name')
                                    ->prototype('scalar')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
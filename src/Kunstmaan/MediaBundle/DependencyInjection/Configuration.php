<?php

namespace Kunstmaan\MediaBundle\DependencyInjection;

use Kunstmaan\MediaBundle\Utils\SymfonyVersion;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    private const DEFAULT_ALLOWED_EXTENSIONS = [
        'bmp',
        'csv',
        'doc',
        'docx',
        'gif',
        'ico',
        'jpeg',
        'jpg',
        'mkv',
        'mp3',
        'mp4',
        'mpeg',
        'ogg',
        'pdf',
        'png',
        'pps',
        'ppsx',
        'ppt',
        'pptx',
        'tif',
        'tiff',
        'txt',
        'wav',
        'webm',
        'webp',
        'xlsx',
    ];

    private const DEFAULT_IMAGE_EXTENSIONS = [
        'bmp',
        'ico',
        'jpeg',
        'jpg',
        'png',
        'tif',
        'tiff',
    ];

    /**
     * Generates the configuration tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('kunstmaan_media');
        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('kunstmaan_media');
        }

        $rootNode
            ->children()
                ->scalarNode('soundcloud_api_key')->defaultValue('YOUR_CLIENT_ID')->end()
                ->scalarNode('aviary_api_key')->defaultNull()->end()
                ->arrayNode('remote_video')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('vimeo')->defaultTrue()->end()
                        ->booleanNode('youtube')->defaultTrue()->end()
                        ->booleanNode('dailymotion')->defaultTrue()->end()
                    ->end()
                ->end()
                ->booleanNode('enable_pdf_preview')->defaultFalse()->end()
                ->arrayNode('allowed_extensions')
                    ->defaultValue(self::DEFAULT_ALLOWED_EXTENSIONS)
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('image_extensions')
                    ->defaultValue(self::DEFAULT_IMAGE_EXTENSIONS)
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('blacklisted_extensions')
                    ->defaultValue(array('php', 'htaccess'))
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('web_root')
                    ->defaultValue(SymfonyVersion::getRootWebPath())
                    ->cannotBeEmpty()
                ->end()
            ->end();

        return $treeBuilder;
    }
}

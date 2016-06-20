<?php

namespace Kunstmaan\NodeSearchBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KunstmaanNodeSearchExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        if (!empty($config['enable_update_listener']) && $config['enable_update_listener']) {
            $loader->load('update_listener.yml');
        }

        if (array_key_exists('use_match_query_for_title', $config)) {
            $container->getDefinition('kunstmaan_node_search.search.node')
                ->addMethodCall('setUseMatchQueryForTitle', [$config['use_match_query_for_title']]);
        }

        $container->getDefinition('kunstmaan_node_search.search_configuration.node')
            ->addMethodCall('setDefaultProperties', [$config['mapping']]);
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $container->prependExtensionConfig('kunstmaan_node_search', [
            'mapping' => [
                'root_id'            => [
                    'type'           => 'integer',
                    'include_in_all' => false,
                    'index'          => 'not_analyzed'
                ],
                'node_id'            => [
                    'type'           => 'integer',
                    'include_in_all' => false,
                    'index'          => 'not_analyzed'
                ],
                'nodetranslation_id' => [
                    'type'           => 'integer',
                    'include_in_all' => false,
                    'index'          => 'not_analyzed'
                ],
                'nodeversion_id'     => [
                    'type'           => 'integer',
                    'include_in_all' => false,
                    'index'          => 'not_analyzed'
                ],
                'title'              => [
                    'type'           => 'string',
                    'boost'          => 2,
                    'include_in_all' => true
                ],
                'slug'               => [
                    'type'           => 'string',
                    'include_in_all' => false,
                    'index'          => 'not_analyzed'
                ],
                'type'               => [
                    'type'           => 'string',
                    'include_in_all' => false,
                    'index'          => 'not_analyzed'
                ],
                'page_class'         => [
                    'type'           => 'string',
                    'include_in_all' => false,
                    'index'          => 'not_analyzed'
                ],
                'content'            => [
                    'type'           => 'string',
                    'include_in_all' => true
                ],
                'created'            => [
                    'type'           => 'date',
                    'include_in_all' => false,
                    'index'          => 'not_analyzed'
                ],
                'updated'            => [
                    'type'           => 'date',
                    'include_in_all' => false,
                    'index'          => 'not_analyzed'
                ],
                'view_roles'         => [
                    'type'           => 'string',
                    'include_in_all' => true,
                    'index'          => 'not_analyzed',
                ],
            ]
        ]);
    }
}

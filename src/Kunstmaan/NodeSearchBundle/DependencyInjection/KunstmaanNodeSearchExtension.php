<?php

namespace Kunstmaan\NodeSearchBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class KunstmaanNodeSearchExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        if (!empty($config['enable_update_listener']) && $config['enable_update_listener']) {
            $loader->load('update_listener.yml');
        }

        if (\array_key_exists('use_match_query_for_title', $config)) {
            $container->getDefinition('kunstmaan_node_search.search.node')
                ->addMethodCall('setUseMatchQueryForTitle', [$config['use_match_query_for_title']]);
        }

        $container->getDefinition('kunstmaan_node_search.search_configuration.node')
            ->addMethodCall('setDefaultProperties', [$config['mapping']]);

        $container->setParameter('kunstmaan_node_search.contexts', $config['contexts']);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $mapping = [
            'mapping' => [
                'root_id' => [
                    'type' => 'integer',
                ],
                'node_id' => [
                    'type' => 'integer',
                ],
                'nodetranslation_id' => [
                    'type' => 'integer',
                ],
                'nodeversion_id' => [
                    'type' => 'integer',
                ],
                'title' => [
                    'type' => 'text',
                ],
                'slug' => [
                    'type' => 'text',
                ],
                'type' => [
                    'type' => 'keyword',
                ],
                'page_class' => [
                    'type' => 'keyword',
                ],
                'content' => [
                    'type' => 'text',
                ],
                'view_roles' => [
                    'type' => 'keyword',
                ],
            ],
        ];

        $container->prependExtensionConfig('kunstmaan_node_search', $mapping);
    }
}

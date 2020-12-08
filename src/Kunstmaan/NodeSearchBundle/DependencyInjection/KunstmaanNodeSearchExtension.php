<?php

namespace Kunstmaan\NodeSearchBundle\DependencyInjection;

use Kunstmaan\NodeSearchBundle\Helper\ElasticSearchUtil;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class KunstmaanNodeSearchExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @var bool
     */
    private $useElasticSearchVersion6;

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration($this->useElasticSearchVersion6);
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

        if (class_exists(\Elastica\Type\Mapping::class)) {
            $nodeSearch = $container->getDefinition('kunstmaan_node_search.search.node');
            $nodeSearch->addMethodCall('setIndexType', ['%kunstmaan_node_search.indextype%']);
        }
    }

    /**
     * Allow an extension to prepend the extension configurations.
     */
    public function prepend(ContainerBuilder $container)
    {
        $this->useElasticSearchVersion6 = $this->useElasticSearchV6($container);

        if ($this->useElasticSearchVersion6) {
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
        } else {
            $mapping = [
                'mapping' => [
                    'root_id' => [
                        'type' => 'integer',
                        'include_in_all' => false,
                        'index' => 'not_analyzed',
                    ],
                    'node_id' => [
                        'type' => 'integer',
                        'include_in_all' => false,
                        'index' => 'not_analyzed',
                    ],
                    'nodetranslation_id' => [
                        'type' => 'integer',
                        'include_in_all' => false,
                        'index' => 'not_analyzed',
                    ],
                    'nodeversion_id' => [
                        'type' => 'integer',
                        'include_in_all' => false,
                        'index' => 'not_analyzed',
                    ],
                    'title' => [
                        'type' => 'string',
                        'include_in_all' => true,
                    ],
                    'slug' => [
                        'type' => 'string',
                        'include_in_all' => false,
                        'index' => 'not_analyzed',
                    ],
                    'type' => [
                        'type' => 'string',
                        'include_in_all' => false,
                        'index' => 'not_analyzed',
                    ],
                    'page_class' => [
                        'type' => 'string',
                        'include_in_all' => false,
                        'index' => 'not_analyzed',
                    ],
                    'content' => [
                        'type' => 'string',
                        'include_in_all' => true,
                    ],
                    'created' => [
                        'type' => 'date',
                        'include_in_all' => false,
                        'index' => 'not_analyzed',
                    ],
                    'updated' => [
                        'type' => 'date',
                        'include_in_all' => false,
                        'index' => 'not_analyzed',
                    ],
                    'view_roles' => [
                        'type' => 'string',
                        'include_in_all' => true,
                        'index' => 'not_analyzed',
                    ],
                ],
            ];
        }

        $container->prependExtensionConfig('kunstmaan_node_search', $mapping);
    }

    private function useElasticSearchV6(ContainerBuilder $container)
    {
        if ($container->hasParameter('kunstmaan_search.elasticsearch_version')) {
            $version = (string) $container->getParameter('kunstmaan_search.elasticsearch_version');
            if (!preg_match('/^(\d+\.)?(\d+\.)?(\*|\d+)$/', $version)) {
                throw new \InvalidArgumentException('Invalid value for "%kunstmaan_search.elasticsearch_version%" parameter, expected value must match a format like "6", "6.1" or "6.1.2".');
            }

            if (version_compare($version, '5.0.0', '>=')) {
                return true;
            }

            return false;
        }

        $hosts = [];
        if ($container->hasParameter('kunstmaan_search.hostname') && $container->hasParameter('kunstmaan_search.port')) {
            $host = $container->getParameter('kunstmaan_search.hostname') . ':' . $container->getParameter('kunstmaan_search.port');

            if ($container->hasParameter('kunstmaan_search.username') && $container->hasParameter('kunstmaan_search.password') &&
                null !== $container->getParameter('kunstmaan_search.username') && null !== $container->getParameter('kunstmaan_search.password')) {
                $host = sprintf(
                    '%s:%s@%s',
                    $container->getParameter('kunstmaan_search.username'),
                    $container->getParameter('kunstmaan_search.password'),
                    $host
                );
            }

            $hosts[] = $host;
        }

        return ElasticSearchUtil::useVersion6($hosts);
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration($this->useElasticSearchV6($container));
    }
}

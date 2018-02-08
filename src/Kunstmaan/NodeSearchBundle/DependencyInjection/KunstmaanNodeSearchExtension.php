<?php

namespace Kunstmaan\NodeSearchBundle\DependencyInjection;

use Kunstmaan\NodeSearchBundle\Configuration\NodePagesConfiguration;
use Kunstmaan\NodeSearchBundle\EventListener\NodeIndexUpdateEventListener;
use Kunstmaan\NodeSearchBundle\Helper\IndexablePagePartsService;
use Kunstmaan\NodeSearchBundle\Search\AbstractElasticaSearcher;
use Kunstmaan\NodeSearchBundle\Search\NodeSearcher;
use Kunstmaan\NodeSearchBundle\Services\SearchService;
use Kunstmaan\NodeSearchBundle\Twig\KunstmaanNodeSearchTwigExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

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
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if (!empty($config['enable_update_listener']) && $config['enable_update_listener']) {
            $loader->load('update_listener.yml');
        }

        // === BEGIN ALIASES ====
        $container->addAliases(
            [
                'kunstmaan_node_search.node_index_update.listener' => new Alias(NodeIndexUpdateEventListener::class),
                'kunstmaan_node_search.search.abstract_elastica_searcher' => new Alias(AbstractElasticaSearcher::class),
                'kunstmaan_node_search.search.node' => new Alias(NodeSearcher::class),
                'kunstmaan_node_search.service.indexable_pageparts' => new Alias(IndexablePagePartsService::class),
                'kunstmaan_node_search.twig.extension' => new Alias(KunstmaanNodeSearchTwigExtension::class),
                'kunstmaan_node_search.search_configuration.node' => new Alias(NodePagesConfiguration::class),
                'kunstmaan_node_search.search.service' => new Alias(SearchService::class),
            ]
        );

        $this->addParameteredAliases(
            $container,
            [
                ['kunstmaan_node_search.node_index_update.listener.class', NodeIndexUpdateEventListener::class, true],
                ['kunstmaan_node_search.search_configuration.node.class', NodePagesConfiguration::class, true],
                ['kunstmaan_node_search.search.node.class', NodeSearcher::class, true],
                ['kunstmaan_node_search.search_service.class', SearchService::class, true],
            ]
        );
        // === END ALIASES ====

        if (array_key_exists('use_match_query_for_title', $config)) {
            $container->getDefinition(NodeSearcher::class)
                ->addMethodCall('setUseMatchQueryForTitle', [$config['use_match_query_for_title']]);
        }

        $container->getDefinition(NodePagesConfiguration::class)
            ->addMethodCall('setDefaultProperties', [$config['mapping']]);

        $container->setParameter('kunstmaan_node_search.contexts', $config['contexts']);
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $aliases
     */
    private function addParameteredAliases(ContainerBuilder $container, $aliases)
    {
        foreach ($aliases as $alias) {
            // Don't allow service with same name as class.
            if ($container->getParameter($alias[0]) !== $alias[1]) {
                $container->setAlias(
                    $container->getParameter($alias[0]),
                    new Alias($alias[1], $alias[2])
                );
            }
        }
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $container->prependExtensionConfig(
            'kunstmaan_node_search',
            [
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
            ]
        );
    }
}

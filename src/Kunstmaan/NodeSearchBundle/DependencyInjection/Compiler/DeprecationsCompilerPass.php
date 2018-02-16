<?php

namespace Kunstmaan\NodeSearchBundle\DependencyInjection\Compiler;

use Kunstmaan\NodeSearchBundle\Configuration\NodePagesConfiguration;
use Kunstmaan\NodeSearchBundle\EventListener\NodeIndexUpdateEventListener;
use Kunstmaan\NodeSearchBundle\Helper\IndexablePagePartsService;
use Kunstmaan\NodeSearchBundle\Search\AbstractElasticaSearcher;
use Kunstmaan\NodeSearchBundle\Search\NodeSearcher;
use Kunstmaan\NodeSearchBundle\Services\SearchService;
use Kunstmaan\NodeSearchBundle\Twig\KunstmaanNodeSearchTwigExtension;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class DeprecationsCompilerPass
 *
 * @package Kunstmaan\NodeSearchBundle\DependencyInjection\Compiler
 */
class DeprecationsCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->addDeprecatedChildDefinitions(
            $container,
            [
                ['kunstmaan_node_search.node_index_update.listener', NodeIndexUpdateEventListener::class],
                ['kunstmaan_node_search.search.abstract_elastica_searcher', AbstractElasticaSearcher::class],
                ['kunstmaan_node_search.search.node', NodeSearcher::class],
                ['kunstmaan_node_search.service.indexable_pageparts', IndexablePagePartsService::class],
                ['kunstmaan_node_search.twig.extension', KunstmaanNodeSearchTwigExtension::class],
                ['kunstmaan_node_search.search_configuration.node', NodePagesConfiguration::class],
                ['kunstmaan_node_search.search.service', SearchService::class],
            ]
        );

        $this->addDeprecatedChildDefinitions(
            $container,
            [
                ['kunstmaan_node_search.node_index_update.listener.class', NodeIndexUpdateEventListener::class],
                ['kunstmaan_node_search.search_configuration.node.class', NodePagesConfiguration::class],
                ['kunstmaan_node_search.search.node.class', NodeSearcher::class],
                ['kunstmaan_node_search.search_service.class', SearchService::class],
            ],
            true
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $deprecations
     * @param bool             $parametered
     */
    private function addDeprecatedChildDefinitions(ContainerBuilder $container, array $deprecations, $parametered = false)
    {
        foreach ($deprecations as $deprecation) {
            // Don't allow service with same name as class.
            if ($parametered && $container->getParameter($deprecation[0]) === $deprecation[1]) {
                continue;
            }

            $definition = new ChildDefinition($deprecation[1]);
            if (isset($deprecation[2])) {
                $definition->setPublic($deprecation[2]);
            }

            if ($parametered) {
                $class = $container->getParameter($deprecation[0]);
                $definition->setClass($class);
                $definition->setDeprecated(
                    true,
                    'Override service class with "%service_id%" is deprecated since KunstmaanNodeSearchBundle 5.1 and will be removed in 6.0. Override the service instance instead.'
                );
                $container->setDefinition($class, $definition);
            } else {
                $definition->setClass($deprecation[1]);
                $definition->setDeprecated(
                    true,
                    'Passing a "%service_id%" instance is deprecated since KunstmaanNodeSearchBundle 5.1 and will be removed in 6.0. Use the FQCN instead.'
                );
                $container->setDefinition($deprecation[0], $definition);
            }
        }
    }
}

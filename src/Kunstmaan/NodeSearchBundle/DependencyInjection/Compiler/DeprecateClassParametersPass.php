<?php

namespace Kunstmaan\NodeSearchBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @internal
 */
final class DeprecateClassParametersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $expectedValues = [
            'kunstmaan_node_search.search_configuration.node.class' => \Kunstmaan\NodeSearchBundle\Configuration\NodePagesConfiguration::class,
            'kunstmaan_node_search.search.node.class' => \Kunstmaan\NodeSearchBundle\Search\NodeSearcher::class,
            'kunstmaan_node_search.search_service.class' => \Kunstmaan\NodeSearchBundle\Services\SearchService::class,
            'kunstmaan_node_search.node_index_update.listener.class' => \Kunstmaan\NodeSearchBundle\EventListener\NodeIndexUpdateEventListener::class,
        ];

        foreach ($expectedValues as $parameter => $expectedValue) {
            if (false === $container->hasParameter($parameter)) {
                continue;
            }

            $currentValue = $container->getParameter($parameter);
            if ($currentValue !== $expectedValue) {
                @trigger_error(sprintf('Using the "%s" parameter to change the class of the service definition is deprecated in KunstmaanNodeSearchBundle 5.2 and will be removed in KunstmaanNodeSearchBundle 6.0. Use service decoration or a service alias instead.', $parameter), E_USER_DEPRECATED);
            }
        }
    }
}

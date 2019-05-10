<?php

namespace Kunstmaan\SearchBundle\DependencyInjection\Compiler;

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
            'kunstmaan_search.search_configuration_chain.class' => \Kunstmaan\SearchBundle\Configuration\SearchConfigurationChain::class,
            'kunstmaan_search.search_provider_chain.class' => \Kunstmaan\SearchBundle\Provider\SearchProviderChain::class,
            'kunstmaan_search.search.class' => \Kunstmaan\SearchBundle\Search\Search::class,
            'kunstmaan_search.search_provider.elastica.class' => \Kunstmaan\SearchBundle\Provider\ElasticaProvider::class,
            'kunstmaan_search.search.factory.analysis.class' => \Kunstmaan\SearchBundle\Search\LanguageAnalysisFactory::class,
        ];

        foreach ($expectedValues as $parameter => $expectedValue) {
            if (false === $container->hasParameter($parameter)) {
                continue;
            }

            $currentValue = $container->getParameter($parameter);
            if ($currentValue !== $expectedValue) {
                @trigger_error(sprintf('Using the "%s" parameter to change the class of the service definition is deprecated in KunstmaanSearchBundle 5.2 and will be removed in KunstmaanSearchBundle 6.0. Use service decoration or a service alias instead.', $parameter), E_USER_DEPRECATED);
            }
        }
    }
}

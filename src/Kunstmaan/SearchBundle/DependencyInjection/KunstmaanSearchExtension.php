<?php

namespace Kunstmaan\SearchBundle\DependencyInjection;

use Kunstmaan\SearchBundle\Command\DeleteIndexCommand;
use Kunstmaan\SearchBundle\Command\PopulateIndexCommand;
use Kunstmaan\SearchBundle\Command\SetupIndexCommand;
use Kunstmaan\SearchBundle\Configuration\SearchConfigurationChain;
use Kunstmaan\SearchBundle\Provider\ElasticaProvider;
use Kunstmaan\SearchBundle\Provider\SearchProviderChain;
use Kunstmaan\SearchBundle\Provider\SearchProviderChainInterface;
use Kunstmaan\SearchBundle\Search\LanguageAnalysisFactory;
use Kunstmaan\SearchBundle\Search\Search;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KunstmaanSearchExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if (count($config['analyzer_languages']) <= 0) {
            $config['analyzer_languages'] = $this->getDefaultAnalyzerLanguages();
        }
        $container->setParameter('analyzer_languages', \array_change_key_case($config['analyzer_languages']), \CASE_LOWER);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        // === BEGIN ALIASES ====
        $container->addAliases(
            [
                'kunstmaan_search.search' => new Alias(Search::class),
                'kunstmaan_search.search.factory.analysis' => new Alias(LanguageAnalysisFactory::class),
                'kunstmaan_search.search_provider_chain' => new Alias(SearchProviderChain::class),
                SearchProviderChainInterface::class => new Alias(SearchProviderChain::class),
                'kunstmaan_search.search_provider.elastica' => new Alias(ElasticaProvider::class),
                'kunstmaan_search.search_configuration_chain' => new Alias(SearchConfigurationChain::class),
                'kunstmaan_search.command.setup' => new Alias(SetupIndexCommand::class),
                'kunstmaan_search.command.delete' => new Alias(DeleteIndexCommand::class),
                'kunstmaan_search.command.populate' => new Alias(PopulateIndexCommand::class),
            ]
        );

        $this->addParameteredAliases(
            $container,
            [
                ['kunstmaan_search.search_configuration_chain.class', SearchConfigurationChain::class, true],
                ['kunstmaan_search.search_provider_chain.class', SearchProviderChain::class, true],
                ['kunstmaan_search.search.class', Search::class, true],
                ['kunstmaan_search.search_provider.elastica.class', ElasticaProvider::class, true],
                ['kunstmaan_search.search.factory.analysis.class', LanguageAnalysisFactory::class, true],
            ]
        );
        // === END ALIASES ====
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

    public function getDefaultAnalyzerLanguages()
    {
        return Yaml::parse(file_get_contents(__DIR__.'/../Resources/config/analyzer_languages.yml'));
    }
}

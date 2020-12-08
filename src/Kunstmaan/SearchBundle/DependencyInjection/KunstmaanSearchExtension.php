<?php

namespace Kunstmaan\SearchBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

class KunstmaanSearchExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if (\count($config['analyzer_languages']) <= 0) {
            $config['analyzer_languages'] = $this->getDefaultAnalyzerLanguages();
        }
        $container->setParameter('analyzer_languages', \array_change_key_case($config['analyzer_languages'], CASE_LOWER));

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $this->loadConnectionParameters($container, $config);

        $this->addSearchIndexPrefixParameter($container, $config);
    }

    public function getDefaultAnalyzerLanguages()
    {
        return Yaml::parse(file_get_contents(__DIR__ . '/../Resources/config/analyzer_languages.yml'));
    }

    private function loadConnectionParameters(ContainerBuilder $container, array $config)
    {
        $this->addHostParameter($container, $config);
        $this->addPortParameter($container, $config);
        $this->addUserParameter($container, $config);
        $this->addPasswordParameter($container, $config);
    }

    private function addHostParameter(ContainerBuilder $container, array $config)
    {
        $searchHost = $container->hasParameter('kunstmaan_search.hostname') ? $container->getParameter('kunstmaan_search.hostname') : 'localhost';
        if (null === $config['connection']['host'] && $searchHost !== 'localhost') {
            @trigger_error('Not providing a value for the "kunstmaan_search.connection.host" config while setting the "kunstmaan_search.hostname" parameter is deprecated since KunstmaanDashboardBundle 5.2, this config value will replace the "kunstmaan_search.hostname" parameter in KunstmaanDashboardBundle 6.0.', E_USER_DEPRECATED);
        }

        if (null !== $config['connection']['host']) {
            $searchHost = $config['connection']['host'];
        }

        $container->setParameter('kunstmaan_search.hostname', $searchHost);
    }

    private function addPortParameter(ContainerBuilder $container, array $config)
    {
        $searchPort = $container->hasParameter('kunstmaan_search.port') ? $container->getParameter('kunstmaan_search.port') : 9200;
        if (null === $config['connection']['port'] && $searchPort !== 9200) {
            @trigger_error('Not providing a value for the "kunstmaan_search.connection.port" config while setting the "kunstmaan_search.port" parameter is deprecated since KunstmaanDashboardBundle 5.2, this config value will replace the "kunstmaan_search.port" parameter in KunstmaanDashboardBundle 6.0.', E_USER_DEPRECATED);
        }

        if (null !== $config['connection']['port']) {
            $searchPort = $config['connection']['port'];
        }

        $container->setParameter('kunstmaan_search.port', $searchPort);
    }

    private function addUserParameter(ContainerBuilder $container, array $config)
    {
        $searchUsername = $container->hasParameter('kunstmaan_search.username') ? $container->getParameter('kunstmaan_search.username') : null;
        if (null === $config['connection']['username'] && null !== $searchUsername) {
            @trigger_error('Not providing a value for the "kunstmaan_search.connection.username" config while setting the "kunstmaan_search.username" parameter is deprecated since KunstmaanDashboardBundle 5.2, this config value will replace the "kunstmaan_search.username" parameter in KunstmaanDashboardBundle 6.0.', E_USER_DEPRECATED);
        }

        if (null !== $config['connection']['username']) {
            $searchUsername = $config['connection']['username'];
        }

        $container->setParameter('kunstmaan_search.username', $searchUsername);
    }

    private function addPasswordParameter(ContainerBuilder $container, array $config)
    {
        $searchPassword = $container->hasParameter('kunstmaan_search.password') ? $container->getParameter('kunstmaan_search.password') : null;
        if (null === $config['connection']['password'] && null !== $searchPassword) {
            @trigger_error('Not providing a value for the "kunstmaan_search.connection.password" config while setting the "kunstmaan_search.password" parameter is deprecated since KunstmaanDashboardBundle 5.2, this config value will replace the "kunstmaan_search.password" parameter in KunstmaanDashboardBundle 6.0.', E_USER_DEPRECATED);
        }

        if (null !== $config['connection']['password']) {
            $searchPassword = $config['connection']['password'];
        }

        $container->setParameter('kunstmaan_search.password', $searchPassword);
    }

    private function addSearchIndexPrefixParameter(ContainerBuilder $container, array $config)
    {
        $indexPrefix = $container->hasParameter('searchindexprefix') ? $container->getParameter('searchindexprefix') : null;
        if (null === $config['index_prefix'] && null !== $indexPrefix) {
            @trigger_error('Not providing a value for the "kunstmaan_search.index_prefix" config while setting the "searchindexprefix" parameter is deprecated since KunstmaanDashboardBundle 5.2, this config value will replace the "searchindexprefix" parameter in KunstmaanDashboardBundle 6.0.', E_USER_DEPRECATED);
        }

        if (null !== $config['index_prefix']) {
            $indexPrefix = $config['index_prefix'];
        }

        $container->setParameter('kunstmaan_search.index_prefix', $indexPrefix);
    }
}

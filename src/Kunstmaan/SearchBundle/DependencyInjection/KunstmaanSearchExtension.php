<?php

namespace Kunstmaan\SearchBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Yaml\Yaml;

class KunstmaanSearchExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
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

        $container->setParameter('kunstmaan_search.index_prefix', $config['index_prefix']);
    }

    public function getDefaultAnalyzerLanguages()
    {
        return Yaml::parse(file_get_contents(__DIR__ . '/../Resources/config/analyzer_languages.yml'));
    }

    private function loadConnectionParameters(ContainerBuilder $container, array $config): void
    {
        $container->setParameter('kunstmaan_search.hostname', $config['connection']['host']);
        $container->setParameter('kunstmaan_search.port', $config['connection']['port']);
        $container->setParameter('kunstmaan_search.username', $config['connection']['username']);
        $container->setParameter('kunstmaan_search.password', $config['connection']['password']);
    }
}

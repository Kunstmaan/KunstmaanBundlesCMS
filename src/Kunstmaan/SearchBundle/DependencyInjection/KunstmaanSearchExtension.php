<?php

namespace Kunstmaan\SearchBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
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
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if (count($config['analyzer_languages']) <= 0) {
            $config['analyzer_languages'] = $this->getDefaultAnalyzerLanguages();
        }
        $container->setParameter('analyzer_languages', \array_change_key_case($config['analyzer_languages']), \CASE_LOWER);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

    public function getDefaultAnalyzerLanguages()
    {
        return Yaml::parse(file_get_contents(__DIR__ . '/../Resources/config/analyzer_languages.yml'));
    }
}

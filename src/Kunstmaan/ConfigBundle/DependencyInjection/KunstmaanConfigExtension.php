<?php

namespace Kunstmaan\ConfigBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;

class KunstmaanConfigExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $backendConfiguration = $this->processConfiguration(new Configuration(), $configs);
        $container->setParameter('kunstmaan_config', $backendConfiguration);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }
}

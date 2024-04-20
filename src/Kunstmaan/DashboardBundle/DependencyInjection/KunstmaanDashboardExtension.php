<?php

namespace Kunstmaan\DashboardBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;

class KunstmaanDashboardExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('commands.yml');

        $container->setParameter('kunstmaan_dashboard.google_analytics.api.client_id', $config['google_analytics']['api']['client_id']);
        $container->setParameter('kunstmaan_dashboard.google_analytics.api.client_secret', $config['google_analytics']['api']['client_secret']);
        $container->setParameter('kunstmaan_dashboard.google_analytics.api.dev_key', $config['google_analytics']['api']['dev_key']);
        $container->setParameter('kunstmaan_dashboard.google_analytics.api.app_name', $config['google_analytics']['api']['app_name']);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('framework', ['esi' => ['enabled' => true]]);
        $container->prependExtensionConfig('kunstmaan_admin', ['dashboard_route' => 'kunstmaan_dashboard']);
    }
}

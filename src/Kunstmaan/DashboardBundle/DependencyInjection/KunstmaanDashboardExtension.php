<?php

namespace Kunstmaan\DashboardBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class KunstmaanDashboardExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('commands.yml');

        $this->loadGoogleAnalyticsParameters($container, $config);
    }

    public function prepend(ContainerBuilder $container)
    {
        $container->prependExtensionConfig('framework', ['esi' => ['enabled' => true]]);
        $container->prependExtensionConfig('kunstmaan_admin', ['dashboard_route' => 'kunstmaan_dashboard']);
    }

    private function loadGoogleAnalyticsParameters(ContainerBuilder $container, array $config)
    {
        $this->addApplicationNameParameter($container, $config);
        $this->addClientIdParameter($container, $config);
        $this->addClientSecretParameter($container, $config);
        $this->addDeveloperKeyParameter($container, $config);
    }

    private function addClientIdParameter(ContainerBuilder $container, array $config)
    {
        $clientId = $container->hasParameter('google.api.client_id') ? $container->getParameter('google.api.client_id') : '';
        if (null === $config['google_analytics']['api']['client_id'] && $clientId !== '') {
            @trigger_error('Not providing a value for the "kunstmaan_dashboard.google_analytics.api.client_id" config while setting the "google.api.client_id" parameter is deprecated since KunstmaanDashboardBundle 5.2, this config value will replace the "google.api.client_id" parameter in KunstmaanDashboardBundle 6.0.', E_USER_DEPRECATED);
        }

        if (null !== $config['google_analytics']['api']['client_id']) {
            $clientId = $config['google_analytics']['api']['client_id'];
        }

        $container->setParameter('kunstmaan_dashboard.google_analytics.api.client_id', $clientId);
    }

    private function addClientSecretParameter(ContainerBuilder $container, array $config)
    {
        $clientSecret = $container->hasParameter('google.api.client_secret') ? $container->getParameter('google.api.client_secret') : '';
        if (null === $config['google_analytics']['api']['client_secret'] && $clientSecret !== '') {
            @trigger_error('Not providing a value for the "kunstmaan_dashboard.google_analytics.api.client_secret" config while setting the "google.api.client_secret" parameter is deprecated since KunstmaanDashboardBundle 5.2, this config value will replace the "google.api.client_secret" parameter in KunstmaanDashboardBundle 6.0.', E_USER_DEPRECATED);
        }

        if (null !== $config['google_analytics']['api']['client_secret']) {
            $clientSecret = $config['google_analytics']['api']['client_secret'];
        }

        $container->setParameter('kunstmaan_dashboard.google_analytics.api.client_secret', $clientSecret);
    }

    private function addDeveloperKeyParameter(ContainerBuilder $container, array $config)
    {
        $devKey = $container->hasParameter('google.api.dev_key') ? $container->getParameter('google.api.dev_key') : '';
        if (null === $config['google_analytics']['api']['dev_key'] && $devKey !== '') {
            @trigger_error('Not providing a value for the "kunstmaan_dashboard.google_analytics.api.dev_key" config while setting the "google.api.dev_key" parameter is deprecated since KunstmaanDashboardBundle 5.2, this config value will replace the "google.api.dev_key" parameter in KunstmaanDashboardBundle 6.0.', E_USER_DEPRECATED);
        }

        if (null !== $config['google_analytics']['api']['dev_key']) {
            $devKey = $config['google_analytics']['api']['dev_key'];
        }

        $container->setParameter('kunstmaan_dashboard.google_analytics.api.dev_key', $devKey);
    }

    private function addApplicationNameParameter(ContainerBuilder $container, array $config)
    {
        $appName = $container->hasParameter('google.api.app_name') ? $container->getParameter('google.api.app_name') : 'Kuma Analytics Dashboard';
        if (null === $config['google_analytics']['api']['app_name'] && $appName !== '' && $appName !== 'Kuma Analytics Dashboard') {
            @trigger_error('Not providing a value for the "kunstmaan_dashboard.google_analytics.api.app_name" config while setting the "google.api.app_name" parameter is deprecated since KunstmaanDashboardBundle 5.2, this config value will replace the "google.api.app_name" parameter in KunstmaanDashboardBundle 6.0.', E_USER_DEPRECATED);
        }

        if (null !== $config['google_analytics']['api']['app_name']) {
            $appName = $config['google_analytics']['api']['app_name'];
        }

        $container->setParameter('kunstmaan_dashboard.google_analytics.api.app_name', $appName);
    }
}

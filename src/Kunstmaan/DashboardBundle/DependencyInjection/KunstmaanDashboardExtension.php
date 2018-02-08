<?php

namespace Kunstmaan\DashboardBundle\DependencyInjection;

use Kunstmaan\DashboardBundle\Command\GoogleAnalyticsDataCollectCommand;
use Kunstmaan\DashboardBundle\Controller\GoogleAnalyticsController;
use Kunstmaan\DashboardBundle\Helper\Google\Analytics\ConfigHelper;
use Kunstmaan\DashboardBundle\Helper\Google\Analytics\QueryHelper;
use Kunstmaan\DashboardBundle\Helper\Google\Analytics\ServiceHelper;
use Kunstmaan\DashboardBundle\Helper\Google\ClientHelper;
use Kunstmaan\DashboardBundle\Manager\WidgetManager;
use Kunstmaan\DashboardBundle\Widget\DashboardWidget;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KunstmaanDashboardExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        // === BEGIN ALIASES ====
        $container->addAliases(
            [
                'kunstmaan_dashboard.manager.widgets' => new Alias(WidgetManager::class),
                'kunstmaan_dashboard.widget.googleanalytics' => new Alias(DashboardWidget::class),
                'kunstmaan_dashboard.helper.google.client' => new Alias(ClientHelper::class),
                'kunstmaan_dashboard.helper.google.analytics.service' => new Alias(ServiceHelper::class),
                'kunstmaan_dashboard.helper.google.analytics.config' => new Alias(ConfigHelper::class),
                'kunstmaan_dashboard.helper.google.analytics.query' => new Alias(QueryHelper::class),
            ]
        );

        $this->addParameteredAliases(
            $container,
            [
                ['kunstmaan_dashboard.widget.googleanalytics.command', GoogleAnalyticsDataCollectCommand::class, true],
                ['kunstmaan_dashboard.widget.googleanalytics.controller', GoogleAnalyticsController::class, true],
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
}

<?php

namespace Kunstmaan\DashboardBundle\DependencyInjection\Compiler;

use Kunstmaan\DashboardBundle\Command\GoogleAnalyticsDataCollectCommand;
use Kunstmaan\DashboardBundle\Controller\GoogleAnalyticsController;
use Kunstmaan\DashboardBundle\Helper\Google\Analytics\ConfigHelper;
use Kunstmaan\DashboardBundle\Helper\Google\Analytics\QueryHelper;
use Kunstmaan\DashboardBundle\Helper\Google\Analytics\ServiceHelper;
use Kunstmaan\DashboardBundle\Helper\Google\ClientHelper;
use Kunstmaan\DashboardBundle\Manager\WidgetManager;
use Kunstmaan\DashboardBundle\Widget\DashboardWidget;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class DeprecationsCompilerPass
 *
 * @package Kunstmaan\DashboardBundle\DependencyInjection\Compiler
 */
class DeprecationsCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->addDeprecatedChildDefinitions(
            $container,
            [
                ['kunstmaan_dashboard.manager.widgets', WidgetManager::class],
                ['kunstmaan_dashboard.widget.googleanalytics', DashboardWidget::class],
                ['kunstmaan_dashboard.helper.google.client', ClientHelper::class],
                ['kunstmaan_dashboard.helper.google.analytics.service', ServiceHelper::class],
                ['kunstmaan_dashboard.helper.google.analytics.config', ConfigHelper::class],
                ['kunstmaan_dashboard.helper.google.analytics.query', QueryHelper::class],
            ]
        );

        $this->addDeprecatedChildDefinitions(
            $container,
            [
                ['kunstmaan_dashboard.widget.googleanalytics.command', GoogleAnalyticsDataCollectCommand::class],
                ['kunstmaan_dashboard.widget.googleanalytics.controller', GoogleAnalyticsController::class],
            ],
            true
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $deprecations
     * @param bool             $parametered
     */
    private function addDeprecatedChildDefinitions(ContainerBuilder $container, array $deprecations, $parametered = false)
    {
        foreach ($deprecations as $deprecation) {
            // Don't allow service with same name as class.
            if ($parametered && $container->getParameter($deprecation[0]) === $deprecation[1]) {
                continue;
            }

            $definition = new ChildDefinition($deprecation[1]);
            if (isset($deprecation[2])) {
                $definition->setPublic($deprecation[2]);
            }

            if ($parametered) {
                $class = $container->getParameter($deprecation[0]);
                $definition->setClass($class);
                $definition->setDeprecated(
                    true,
                    'Override service class with "%service_id%" is deprecated since KunstmaanDashboardBundle 5.1 and will be removed in 6.0. Override the service instance instead.'
                );
                $container->setDefinition($class, $definition);
            } else {
                $definition->setClass($deprecation[1]);
                $definition->setDeprecated(
                    true,
                    'Passing a "%service_id%" instance is deprecated since KunstmaanDashboardBundle 5.1 and will be removed in 6.0. Use the FQCN instead.'
                );
                $container->setDefinition($deprecation[0], $definition);
            }
        }
    }
}

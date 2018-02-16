<?php

namespace Kunstmaan\DashboardBundle\DependencyInjection\Compiler;

use Kunstmaan\DashboardBundle\Manager\WidgetManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class WidgetCompilerPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(WidgetManager::class)) {
            return;
        }

        $definition = $container->getDefinition(WidgetManager::class);

        foreach ($container->findTaggedServiceIds('kunstmaan_dashboard.widget') as $id => $tags) {
            foreach ($tags as $tag) {
                if (!empty($tag['method'])) {
                    $widget = [new Reference($id), $tag['method']];
                } else {
                    $widget = new Reference($id);
                }
                $definition->addMethodCall('addWidget', [$widget]);
            }
        }
    }
}

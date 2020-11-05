<?php

namespace Kunstmaan\AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This compiler pass makes it possible to add data collectors to the admin toolbar
 */
class DataCollectorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('kunstmaan_admin.toolbar.datacollector')) {
            return;
        }

        $definition = $container->getDefinition('kunstmaan_admin.toolbar.datacollector');
        $taggedServices = $container->findTaggedServiceIds('kunstmaan_admin.toolbar_collector');

        // Check if debug enabled
        $debug = $container->hasDefinition('profiler');
        // Check if toolbar enabled
        $enabled = $container->getParameter('kunstmaan_admin.enable_toolbar_helper');

        if (!$enabled) {
            return;
        }

        // Add the template first.
        foreach ($taggedServices as $id => $attributes) {
            $taggedService = $container->getDefinition($id);
            $tag = $taggedService->getTag('kunstmaan_admin.toolbar_collector');

            if (isset($tag[0]) && isset($tag[0]['template'])) {
                $taggedService->addMethodCall('setTemplate', [$tag[0]['template']]);
            }
        }

        // If debug is true, do not add a new toolbar, but add the datacollectors to the symfony toolbar.
        // Code taken from the ProfilerPass class.
        if ($debug) {
            $definition = $container->getDefinition('profiler');

            $collectors = new \SplPriorityQueue();
            $order = PHP_INT_MAX;
            foreach ($taggedServices as $id => $attributes) {
                $priority = isset($attributes[0]['priority']) ? $attributes[0]['priority'] : 0;
                $template = null;

                if (isset($attributes[0]['template'])) {
                    if (!isset($attributes[0]['id'])) {
                        throw new \InvalidArgumentException(sprintf('Data collector service "%s" must have an id attribute in order to specify a template', $id));
                    }
                    $template = [$attributes[0]['id'], $attributes[0]['template']];
                }

                $collectors->insert([$id, $template], [$priority, --$order]);
            }

            $templates = [];
            foreach ($collectors as $collector) {
                $definition->addMethodCall('add', [new Reference($collector[0])]);
                $templates[$collector[0]] = $collector[1];
            }

            $originalTemplates = $container->getParameter('data_collector.templates');

            $container->setParameter('data_collector.templates', array_merge($originalTemplates, $templates));
        } else {
            foreach ($taggedServices as $id => $tags) {
                $definition->addMethodCall('addDataCollector', [new Reference($id)]);
            }
        }
    }
}

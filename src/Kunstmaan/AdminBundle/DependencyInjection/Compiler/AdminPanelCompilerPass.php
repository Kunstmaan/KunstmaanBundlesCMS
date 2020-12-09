<?php

namespace Kunstmaan\AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This compiler pass makes it possible to add items to the admin panel
 */
class AdminPanelCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('kunstmaan_admin.admin_panel')) {
            return;
        }

        $definition = $container->getDefinition('kunstmaan_admin.admin_panel');

        foreach ($container->findTaggedServiceIds('kunstmaan_admin.admin_panel.adaptor') as $id => $attributes) {
            $priority = isset($attributes[0]['priority']) ? $attributes[0]['priority'] : 0;

            $definition->addMethodCall('addAdminPanelAdaptor', [new Reference($id), $priority]);
        }
    }
}

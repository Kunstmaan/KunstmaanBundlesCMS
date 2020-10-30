<?php

namespace Kunstmaan\AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * NEXT_MAJOR: remove compiler pass
 *
 * @internal
 */
final class ConvertFosUserParametersCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->getParameter('kunstmaan_admin.enable_new_cms_authentication')) {
            return;
        }

        $container->setParameter('kunstmaan_admin.user_class', $container->getParameter('fos_user.model.user.class'));
        $container->setParameter('kunstmaan_admin.group_class', $container->getParameter('fos_user.model.group.class'));
        $container->setDefinition('kunstmaan_admin.user_manager', $container->getDefinition('fos_user.user_manager.default'));
        $container->setDefinition('kunstmaan_admin.group_manager', $container->getDefinition('fos_user.group_manager.default'));
    }
}

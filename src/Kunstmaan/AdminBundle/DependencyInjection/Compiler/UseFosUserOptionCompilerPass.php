<?php

namespace Kunstmaan\AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class UseFosUserOptionCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $enableCustomLogin = $container->getParameter('kunstmaan_admin.enable_custom_login');
        if (!$enableCustomLogin) {
            @trigger_error('Using FosUserBundle routing and services is deprecated since KunstmaanAdminBundle 5.8 and will be removed in KunstmaanAdminBundle 6.0. Use our custom implementation instead', E_USER_DEPRECATED);
            $container->setParameter('kunstmaan_admin.user_class', $container->getParameter('fos_user.model.user.class'));
            $container->setParameter('kunstmaan_admin.group_class', $container->getParameter('fos_user.model.group.class'));
            $container->setDefinition('kunstmaan_admin.user_manager', $container->getDefinition('fos_user.user_manager.default'));
            $container->setDefinition('kunstmaan_admin.group_manager', $container->getDefinition('fos_user.group_manager.default'));
        }
    }
}

<?php

namespace Kunstmaan\UserManagementBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FixUserManagerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        // Mark the fos user manager and related aliases specifically as public.
        $container->getAlias('fos_user.user_manager')->setPublic(true);
        $container->getAlias('fos_user.group_manager')->setPublic(true);
    }
}
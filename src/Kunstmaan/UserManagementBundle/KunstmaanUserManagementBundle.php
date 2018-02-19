<?php

namespace Kunstmaan\UserManagementBundle;

use Kunstmaan\UserManagementBundle\DependencyInjection\Compiler\DeprecationsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class KunstmaanUserManagementBundle
 *
 * @package Kunstmaan\UserManagementBundle
 */
class KunstmaanUserManagementBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new DeprecationsCompilerPass());
    }
}

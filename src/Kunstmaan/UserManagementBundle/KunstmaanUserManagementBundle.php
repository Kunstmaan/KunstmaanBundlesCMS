<?php

namespace Kunstmaan\UserManagementBundle;

use Kunstmaan\UserManagementBundle\DependencyInjection\Compiler\DeprecateClassParametersPass;
use Kunstmaan\UserManagementBundle\DependencyInjection\Compiler\FixUserManagerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KunstmaanUserManagementBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new FixUserManagerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, -1);
        $container->addCompilerPass(new DeprecateClassParametersPass());
    }
}

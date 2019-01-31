<?php

namespace Kunstmaan\AdminListBundle;

use Kunstmaan\AdminListBundle\DependencyInjection\Compiler\DeprecateClassParametersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * KunstmaanAdminListBundle
 */
class KunstmaanAdminListBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DeprecateClassParametersPass());
    }
}

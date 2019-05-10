<?php

namespace Kunstmaan\ConfigBundle;

use Kunstmaan\ConfigBundle\DependencyInjection\Compiler\DeprecateClassParametersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KunstmaanConfigBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DeprecateClassParametersPass());
    }
}

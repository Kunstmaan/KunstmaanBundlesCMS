<?php

namespace Kunstmaan\RedirectBundle;

use Kunstmaan\RedirectBundle\DependencyInjection\Compiler\DeprecateClassParametersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KunstmaanRedirectBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DeprecateClassParametersPass());
    }
}

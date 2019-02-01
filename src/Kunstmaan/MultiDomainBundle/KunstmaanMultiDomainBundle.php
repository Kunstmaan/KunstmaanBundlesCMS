<?php

namespace Kunstmaan\MultiDomainBundle;

use Kunstmaan\MultiDomainBundle\DependencyInjection\CompilerPass\DeprecateClassParametersPass;
use Kunstmaan\MultiDomainBundle\DependencyInjection\CompilerPass\MultidomainConfigurationPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KunstmaanMultiDomainBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new MultidomainConfigurationPass());
        $container->addCompilerPass(new DeprecateClassParametersPass());
    }
}

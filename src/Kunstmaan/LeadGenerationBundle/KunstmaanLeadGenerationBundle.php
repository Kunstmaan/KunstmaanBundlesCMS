<?php

namespace Kunstmaan\LeadGenerationBundle;

use Kunstmaan\LeadGenerationBundle\DependencyInjection\Compiler\DeprecateClassParametersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KunstmaanLeadGenerationBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DeprecateClassParametersPass());
    }
}

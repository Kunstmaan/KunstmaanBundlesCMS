<?php

namespace Kunstmaan\FormBundle;

use Kunstmaan\FormBundle\DependencyInjection\Compiler\DeprecateClassParametersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * KunstmaanFormBundle
 */
class KunstmaanFormBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DeprecateClassParametersPass());
    }
}

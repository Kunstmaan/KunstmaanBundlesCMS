<?php

namespace Kunstmaan\NodeSearchBundle;

use Kunstmaan\NodeSearchBundle\DependencyInjection\Compiler\DeprecateClassParametersPass;
use Kunstmaan\NodeSearchBundle\DependencyInjection\Compiler\NodeSearcherCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * KunstmaanNodeSearchBundle
 */
class KunstmaanNodeSearchBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new NodeSearcherCompilerPass());
        $container->addCompilerPass(new DeprecateClassParametersPass());
    }
}

<?php

namespace Kunstmaan\NodeBundle;

use Kunstmaan\NodeBundle\DependencyInjection\Compiler\DeprecateClassParametersPass;
use Kunstmaan\NodeBundle\DependencyInjection\Compiler\PageRenderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * KunstmaanNodeBundle
 */
class KunstmaanNodeBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new DeprecateClassParametersPass());
        $container->addCompilerPass(new PageRenderPass());
    }
}

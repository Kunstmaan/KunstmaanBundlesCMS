<?php

namespace Kunstmaan\NodeBundle;

use Kunstmaan\NodeBundle\DependencyInjection\Compiler\PageRenderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KunstmaanNodeBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new PageRenderPass());
    }
}

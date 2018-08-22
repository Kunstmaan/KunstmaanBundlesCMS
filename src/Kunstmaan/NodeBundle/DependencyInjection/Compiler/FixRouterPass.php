<?php

namespace Kunstmaan\NodeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FixRouterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // Mark the router alias specifically as public.
        // This compiler pass can be removed when symfony-cmf/routing-bundle is upgraded to >=2.1.0
        $container->getAlias('router')->setPublic(true);
    }
}

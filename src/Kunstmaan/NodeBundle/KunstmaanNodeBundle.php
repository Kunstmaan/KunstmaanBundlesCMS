<?php

namespace Kunstmaan\NodeBundle;

use Kunstmaan\NodeBundle\DependencyInjection\Compiler\HomepagePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * KunstmaanNodeBundle
 */
class KunstmaanNodeBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new HomepagePass());
    }
}

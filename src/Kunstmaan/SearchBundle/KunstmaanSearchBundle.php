<?php

namespace Kunstmaan\SearchBundle;

use Kunstmaan\SearchBundle\DependencyInjection\Compiler\IndexerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * KunstmaanSearchBundle
 */
class KunstmaanSearchBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new IndexerCompilerPass());
    }
}

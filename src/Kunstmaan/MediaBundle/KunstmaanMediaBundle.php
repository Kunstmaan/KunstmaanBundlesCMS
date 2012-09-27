<?php

namespace Kunstmaan\MediaBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;

use Kunstmaan\MediaBundle\DependencyInjection\Compiler\MediaHandlerCompilerPass;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * KunstmaanMediaBundle
 */
class KunstmaanMediaBundle extends Bundle
{

    /**
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new MediaHandlerCompilerPass());
    }
}

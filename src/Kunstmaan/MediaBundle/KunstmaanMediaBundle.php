<?php

namespace Kunstmaan\MediaBundle;

use Kunstmaan\MediaBundle\DependencyInjection\Compiler\MediaHandlerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KunstmaanMediaBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new MediaHandlerCompilerPass());
    }
}

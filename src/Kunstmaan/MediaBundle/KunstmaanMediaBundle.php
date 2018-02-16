<?php

namespace Kunstmaan\MediaBundle;

use Kunstmaan\MediaBundle\DependencyInjection\Compiler\DeprecationsCompilerPass;
use Kunstmaan\MediaBundle\DependencyInjection\Compiler\MediaHandlerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class KunstmaanMediaBundle
 *
 * @package Kunstmaan\MediaBundle
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
        $container->addCompilerPass(new DeprecationsCompilerPass());
    }
}

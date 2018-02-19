<?php

namespace Kunstmaan\UtilitiesBundle;

use Kunstmaan\UtilitiesBundle\DependencyInjection\Compiler\DeprecationsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class KunstmaanUtilitiesBundle
 *
 * @package Kunstmaan\UtilitiesBundle
 */
class KunstmaanUtilitiesBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new DeprecationsCompilerPass());
    }
}

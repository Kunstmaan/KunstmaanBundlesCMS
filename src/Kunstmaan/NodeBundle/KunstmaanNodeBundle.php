<?php

namespace Kunstmaan\NodeBundle;

use Kunstmaan\NodeBundle\DependencyInjection\Compiler\DeprecationsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class KunstmaanNodeBundle
 *
 * @package Kunstmaan\NodeBundle
 */
class KunstmaanNodeBundle extends Bundle
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

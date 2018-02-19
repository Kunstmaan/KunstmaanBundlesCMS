<?php

namespace Kunstmaan\CacheBundle;

use Kunstmaan\CacheBundle\DependencyInjection\Compiler\DeprecationsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class KunstmaanCacheBundle
 *
 * @package Kunstmaan\CacheBundle
 */
class KunstmaanCacheBundle extends Bundle
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

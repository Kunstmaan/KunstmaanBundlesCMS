<?php

namespace Kunstmaan\MultiDomainBundle;

use Kunstmaan\MultiDomainBundle\DependencyInjection\Compiler\DeprecationsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class KunstmaanMultiDomainBundle
 *
 * @package Kunstmaan\MultiDomainBundle
 */
class KunstmaanMultiDomainBundle extends Bundle
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

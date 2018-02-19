<?php

namespace Kunstmaan\SitemapBundle;

use Kunstmaan\SitemapBundle\DependencyInjection\Compiler\DeprecationsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class KunstmaanSitemapBundle
 *
 * @package Kunstmaan\SitemapBundle
 */
class KunstmaanSitemapBundle extends Bundle
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

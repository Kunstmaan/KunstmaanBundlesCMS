<?php

namespace Kunstmaan\SearchBundle;

use Kunstmaan\SearchBundle\DependencyInjection\Compiler\DeprecationsCompilerPass;
use Kunstmaan\SearchBundle\DependencyInjection\Compiler\SearchConfigurationCompilerPass;
use Kunstmaan\SearchBundle\DependencyInjection\Compiler\SearchProviderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class KunstmaanSearchBundle
 *
 * @package Kunstmaan\SearchBundle
 */
class KunstmaanSearchBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new SearchConfigurationCompilerPass());
        $container->addCompilerPass(new SearchProviderCompilerPass());
        $container->addCompilerPass(new DeprecationsCompilerPass());
    }
}

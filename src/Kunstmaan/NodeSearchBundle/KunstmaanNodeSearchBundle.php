<?php

namespace Kunstmaan\NodeSearchBundle;

use Kunstmaan\SearchBundle\DependencyInjection\Compiler\SearchConfigurationCompilerPass;
use Kunstmaan\SearchBundle\DependencyInjection\Compiler\SearchProviderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * KunstmaanNodeSearchBundle
 */
class KunstmaanNodeSearchBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }
}

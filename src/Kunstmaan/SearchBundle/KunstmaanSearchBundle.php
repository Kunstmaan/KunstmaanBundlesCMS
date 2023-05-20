<?php

namespace Kunstmaan\SearchBundle;

use Kunstmaan\SearchBundle\DependencyInjection\Compiler\SearchConfigurationCompilerPass;
use Kunstmaan\SearchBundle\DependencyInjection\Compiler\SearchProviderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KunstmaanSearchBundle extends Bundle
{
    /**
     * @return void
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new SearchConfigurationCompilerPass());
        $container->addCompilerPass(new SearchProviderCompilerPass());
    }
}

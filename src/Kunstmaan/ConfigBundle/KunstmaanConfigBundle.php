<?php

namespace Kunstmaan\ConfigBundle;

use Kunstmaan\ConfigBundle\DependencyInjection\Compiler\KunstmaanConfigConfigurationPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KunstmaanConfigBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new KunstmaanConfigConfigurationPass(), PassConfig::TYPE_BEFORE_REMOVING);
    }
}

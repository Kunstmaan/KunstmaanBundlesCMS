<?php

namespace Kunstmaan\ConfigBundle;

use Kunstmaan\ConfigBundle\DependencyInjection\Compiler\DeprecationsCompilerPass;
use Kunstmaan\ConfigBundle\DependencyInjection\Compiler\KunstmaanConfigConfigurationPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class KunstmaanConfigBundle
 *
 * @package Kunstmaan\ConfigBundle
 */
class KunstmaanConfigBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new KunstmaanConfigConfigurationPass(), PassConfig::TYPE_BEFORE_REMOVING);
        $container->addCompilerPass(new DeprecationsCompilerPass());
    }
}

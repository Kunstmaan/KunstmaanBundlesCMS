<?php

namespace Kunstmaan\NodeBundle;

use Kunstmaan\NodeBundle\DependencyInjection\Compiler\DeprecateClassParametersPass;
use Kunstmaan\NodeBundle\DependencyInjection\Compiler\FixRouterPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * KunstmaanNodeBundle
 */
class KunstmaanNodeBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        // Use -1 priority to run this compiler pass after the symfony-cmf/router compiler pass
        $container->addCompilerPass(new FixRouterPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, -1);
        $container->addCompilerPass(new DeprecateClassParametersPass());
    }
}

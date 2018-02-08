<?php

namespace Kunstmaan\LeadGenerationBundle;

use Kunstmaan\LeadGenerationBundle\DependencyInjection\Compiler\DeprecationsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KunstmaanLeadGenerationBundle extends Bundle
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

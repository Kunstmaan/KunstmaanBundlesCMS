<?php

namespace Kunstmaan\DashboardBundle;

use Kunstmaan\DashboardBundle\DependencyInjection\Compiler\WidgetCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KunstmaanDashboardBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new WidgetCompilerPass());
    }
}

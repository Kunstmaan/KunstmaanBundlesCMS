<?php

namespace Kunstmaan\AdminBundle;

use Kunstmaan\AdminBundle\DependencyInjection\Compiler\AddLogProcessorsCompilerPass;
use Kunstmaan\AdminBundle\DependencyInjection\Compiler\AdminPanelCompilerPass;
use Kunstmaan\AdminBundle\DependencyInjection\Compiler\DataCollectorPass;
use Kunstmaan\AdminBundle\DependencyInjection\Compiler\EnablePermissionsPass;
use Kunstmaan\AdminBundle\DependencyInjection\Compiler\MenuCompilerPass;
use Kunstmaan\AdminBundle\DependencyInjection\KunstmaanAdminExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KunstmaanAdminBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new MenuCompilerPass());
        $container->addCompilerPass(new AdminPanelCompilerPass());
        $container->addCompilerPass(new AddLogProcessorsCompilerPass());
        $container->addCompilerPass(new DataCollectorPass());
        $container->addCompilerPass(new EnablePermissionsPass());

        $container->registerExtension(new KunstmaanAdminExtension());
    }
}

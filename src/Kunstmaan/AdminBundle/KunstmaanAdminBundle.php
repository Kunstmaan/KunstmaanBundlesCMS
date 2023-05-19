<?php

namespace Kunstmaan\AdminBundle;

use Kunstmaan\AdminBundle\DependencyInjection\Compiler\AddLogProcessorsCompilerPass;
use Kunstmaan\AdminBundle\DependencyInjection\Compiler\AdminPanelCompilerPass;
use Kunstmaan\AdminBundle\DependencyInjection\Compiler\DataCollectorPass;
use Kunstmaan\AdminBundle\DependencyInjection\Compiler\EnablePermissionsPass;
use Kunstmaan\AdminBundle\DependencyInjection\Compiler\InjectPasswordHasherPass;
use Kunstmaan\AdminBundle\DependencyInjection\Compiler\MenuCompilerPass;
use Kunstmaan\AdminBundle\DependencyInjection\Compiler\ReplaceSymfonyEnvironmentConfiguratorCompilerPass;
use Kunstmaan\AdminBundle\DependencyInjection\KunstmaanAdminExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KunstmaanAdminBundle extends Bundle
{
    /**
     * @return void
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new MenuCompilerPass());
        $container->addCompilerPass(new AdminPanelCompilerPass());
        $container->addCompilerPass(new AddLogProcessorsCompilerPass());
        $container->addCompilerPass(new DataCollectorPass());
        $container->addCompilerPass(new EnablePermissionsPass());
        $container->addCompilerPass(new InjectPasswordHasherPass());
        $container->addCompilerPass(new ReplaceSymfonyEnvironmentConfiguratorCompilerPass());

        $container->registerExtension(new KunstmaanAdminExtension());
    }
}

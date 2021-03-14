<?php

namespace Kunstmaan\AdminBundle;

use Kunstmaan\AdminBundle\DependencyInjection\Compiler\AddLogProcessorsCompilerPass;
use Kunstmaan\AdminBundle\DependencyInjection\Compiler\AdminPanelCompilerPass;
use Kunstmaan\AdminBundle\DependencyInjection\Compiler\ConsoleCompilerPass;
use Kunstmaan\AdminBundle\DependencyInjection\Compiler\ConvertFosUserParametersCompilerPass;
use Kunstmaan\AdminBundle\DependencyInjection\Compiler\DataCollectorPass;
use Kunstmaan\AdminBundle\DependencyInjection\Compiler\DeprecateClassParametersPass;
use Kunstmaan\AdminBundle\DependencyInjection\Compiler\DomainConfigurationPass;
use Kunstmaan\AdminBundle\DependencyInjection\Compiler\EnablePermissionsPass;
use Kunstmaan\AdminBundle\DependencyInjection\Compiler\InjectUntrackedTokenStorageCompilerPass;
use Kunstmaan\AdminBundle\DependencyInjection\Compiler\MenuCompilerPass;
use Kunstmaan\AdminBundle\DependencyInjection\Compiler\PagerfantaBridgePass;
use Kunstmaan\AdminBundle\DependencyInjection\Compiler\VersionCheckerCacheBcPass;
use Kunstmaan\AdminBundle\DependencyInjection\KunstmaanAdminExtension;
use Kunstmaan\AdminBundle\DependencyInjection\PagerfantaExtension;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * KunstmaanAdminBundle
 */
class KunstmaanAdminBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new MenuCompilerPass());
        $container->addCompilerPass(new AdminPanelCompilerPass());
        $container->addCompilerPass(new AddLogProcessorsCompilerPass());
        $container->addCompilerPass(new DataCollectorPass());
        $container->addCompilerPass(new DomainConfigurationPass());
        $container->addCompilerPass(new ConsoleCompilerPass());
        $container->addCompilerPass(new InjectUntrackedTokenStorageCompilerPass());
        $container->addCompilerPass(new EnablePermissionsPass());
        $container->addCompilerPass(new ConvertFosUserParametersCompilerPass());

        $container->addCompilerPass(new DeprecateClassParametersPass());
        $container->addCompilerPass(new VersionCheckerCacheBcPass());

        $container->registerExtension(new KunstmaanAdminExtension());

        $container->registerExtension(new PagerfantaExtension());
        $container->addCompilerPass(new PagerfantaBridgePass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, -1); // Should run after all passes from BabDevPagerfantaBundle
    }
}

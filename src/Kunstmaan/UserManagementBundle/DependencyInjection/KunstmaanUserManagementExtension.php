<?php

namespace Kunstmaan\UserManagementBundle\DependencyInjection;

use Kunstmaan\UserManagementBundle\AdminList\UserAdminListConfigurator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class KunstmaanUserManagementExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $this->addAdminlistConfiguratorClassParameter($container, $config);
    }

    private function addAdminlistConfiguratorClassParameter(ContainerBuilder $container, array $config)
    {
        if ($container->hasParameter('kunstmaan_user_management.user_admin_list_configurator.class') && $container->getParameter('kunstmaan_user_management.user_admin_list_configurator.class') !== UserAdminListConfigurator::class) {
            @trigger_error('Overriding the user adminlist configurator class with the "kunstmaan_user_management.user_admin_list_configurator.class" parameter is deprecated since KunstmaanUserManagementBundle 5.9 and will not be allowed in KunstmaanUserManagementBundle 6.0. Use the "kunstmaan_user_management.user.adminlist_configurator" config option instead.', E_USER_DEPRECATED);

            return;
        }

        $container->setParameter('kunstmaan_user_management.user_admin_list_configurator.class', $config['user']['adminlist_configurator']);
    }
}

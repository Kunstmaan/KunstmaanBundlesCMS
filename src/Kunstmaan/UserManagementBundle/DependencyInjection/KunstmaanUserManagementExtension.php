<?php

namespace Kunstmaan\UserManagementBundle\DependencyInjection;

use Kunstmaan\UserManagementBundle\AdminList\UserAdminListConfigurator;
use Kunstmaan\UserManagementBundle\Helper\Menu\UserManagementMenuAdaptor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KunstmaanUserManagementExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        // === BEGIN ALIASES ====
        $container->addAliases(
            [
                'kunstmaan_user_management.menu.adaptor' => new Alias(UserManagementMenuAdaptor::class),
            ]
        );

        $this->addParameteredAliases(
            $container,
            [
                ['kunstmaan_user_management.user_admin_list_configurator.class', UserAdminListConfigurator::class, true],
                ['kunstmaan_user_management.menu.adaptor.class', UserManagementMenuAdaptor::class, true],
            ]
        );
        // === END ALIASES ====
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $aliases
     */
    private function addParameteredAliases(ContainerBuilder $container, $aliases)
    {
        foreach ($aliases as $alias) {
            // Don't allow service with same name as class.
            if ($container->getParameter($alias[0]) !== $alias[1]) {
                $container->setAlias(
                    $container->getParameter($alias[0]),
                    new Alias($alias[1], $alias[2])
                );
            }
        }
    }
}

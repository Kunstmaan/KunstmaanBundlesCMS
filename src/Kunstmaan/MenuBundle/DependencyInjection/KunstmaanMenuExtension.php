<?php

namespace Kunstmaan\MenuBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class KunstmaanMenuExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('kunstmaan_menu.menus', $config['menus']);
        $container->setParameter('kunstmaan_menu.entity.menu.class', $config['menu_entity']);
        $container->setParameter('kunstmaan_menu.entity.menuitem.class', $config['menuitem_entity']);
        $container->setParameter('kunstmaan_menu.adminlist.menu_configurator.class', $config['menu_adminlist']);
        $container->setParameter('kunstmaan_menu.adminlist.menuitem_configurator.class', $config['menuitem_adminlist']);
        $container->setParameter('kunstmaan_menu.form.menu_admintype.class', $config['menu_form']);
        $container->setParameter('kunstmaan_menu.form.menuitem_admintype.class', $config['menuitem_form']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }
}

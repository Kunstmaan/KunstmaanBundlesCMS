<?php

namespace Kunstmaan\MenuBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KunstmaanMenuExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
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

	$loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}

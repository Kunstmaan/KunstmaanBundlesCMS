<?php

namespace Kunstmaan\MenuBundle\DependencyInjection;

use Kunstmaan\MenuBundle\Repository\MenuItemRepository;
use Kunstmaan\MenuBundle\Service\MenuAdaptor;
use Kunstmaan\MenuBundle\Service\MenuService;
use Kunstmaan\MenuBundle\Service\RenderService;
use Kunstmaan\MenuBundle\Twig\MenuTwigExtension;
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

        // === BEGIN ALIASES ====
        $container->addAliases(
            [
                'kunstmaan_menu.menu.adaptor' => new Alias(MenuAdaptor::class),
                'kunstmaan_menu.menu.service' => new Alias(MenuService::class),
                'kunstmaan_menu.menu.render_service' => new Alias(RenderService::class),
                'kunstmaan_menu.menu.repository' => new Alias(MenuItemRepository::class),
                'kunstmaan_menu.menu.twig.extension' => new Alias(MenuTwigExtension::class),
            ]
        );

        $this->addParameteredAliases(
            $container,
            [
                ['kunstmaan_menu.menu.adaptor.class', MenuAdaptor::class, true],
                ['kunstmaan_menu.menu.service.class', MenuService::class, true],
                ['kunstmaan_menu.menu.twig.extension.class', MenuTwigExtension::class, true],
                ['kunstmaan_menu.menu.repository.class', MenuItemRepository::class, true],
                ['kunstmaan_menu.menu.render_service.class', RenderService::class, true],
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

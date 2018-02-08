<?php

namespace Kunstmaan\ConfigBundle\DependencyInjection;

use Kunstmaan\ConfigBundle\Controller\ConfigController;
use Kunstmaan\ConfigBundle\Helper\Menu\ConfigMenuAdaptor;
use Kunstmaan\ConfigBundle\Twig\ConfigTwigExtension;
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
class KunstmaanConfigExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $backendConfiguration = $this->processConfiguration(new Configuration(), $configs);
        $container->setParameter('kunstmaan_config', $backendConfiguration);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        // === BEGIN ALIASES ====
        $container->addAliases(
            [
                'kunstmaan_config.menu.adaptor' => new Alias(ConfigMenuAdaptor::class),
                'kunstmaan_config.config.twig.extension' => new Alias(ConfigTwigExtension::class),
                'kunstmaan_config.controller.config' => new Alias(ConfigController::class),
            ]
        );

        $this->addParameteredAliases(
            $container,
            [
                ['kunstmaan_config.menu.adaptor.class', ConfigMenuAdaptor::class, true],
                ['kunstmaan_config.twig.extension.class', ConfigTwigExtension::class, true],
                ['kunstmaan_config.controller.config.class', ConfigController::class, true],
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

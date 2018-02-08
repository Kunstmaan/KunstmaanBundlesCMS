<?php

namespace Kunstmaan\RedirectBundle\DependencyInjection;

use Kunstmaan\RedirectBundle\Form\RedirectAdminType;
use Kunstmaan\RedirectBundle\Helper\Menu\RedirectMenuAdaptor;
use Kunstmaan\RedirectBundle\Repository\RedirectRepository;
use Kunstmaan\RedirectBundle\Router\RedirectRouter;
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
class KunstmaanRedirectExtension extends Extension
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
                'kunstmaan_redirect.menu.adaptor' => new Alias(RedirectMenuAdaptor::class),
                'kunstmaan_redirect.repositories.redirect' => new Alias(RedirectRepository::class),
                'kunstmaan_redirect.redirectrouter' => new Alias(RedirectRouter::class),
                'kunstmaan_redirect.form.type' => new Alias(RedirectAdminType::class),
            ]
        );

        $this->addParameteredAliases(
            $container,
            [
                ['kunstmaan_redirect.menu.adaptor.class', RedirectMenuAdaptor::class, true],
                ['kunstmaan_redirect.redirect_repository.class', RedirectRepository::class, true],
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

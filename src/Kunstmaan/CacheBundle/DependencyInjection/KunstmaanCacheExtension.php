<?php

namespace Kunstmaan\CacheBundle\DependencyInjection;

use Kunstmaan\CacheBundle\EventListener\VarnishListener;
use Kunstmaan\CacheBundle\Helper\Menu\VarnishMenuAdaptor;
use Kunstmaan\CacheBundle\Helper\VarnishHelper;
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
class KunstmaanCacheExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        // === BEGIN ALIASES ====
        $container->addAliases(
            [
                'kunstmaan_cache.menu_adaptor.varnish' => new Alias(VarnishMenuAdaptor::class),
                'kunstmaan_cache.helper.varnish' => new Alias(VarnishHelper::class),
                'kunstmaan_cache.listener.varnish' => new Alias(VarnishListener::class),
            ]
        );

        $this->addParameteredAliases(
            $container,
            [
                ['kunstmaan_cache.menu_adaptor.varnish.class', VarnishMenuAdaptor::class, true],
                ['kunstmaan_cache.helper.varnish.class', VarnishHelper::class, true],
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

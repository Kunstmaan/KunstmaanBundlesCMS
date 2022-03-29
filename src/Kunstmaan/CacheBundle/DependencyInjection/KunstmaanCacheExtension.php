<?php

namespace Kunstmaan\CacheBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class KunstmaanCacheExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $config = [
            'cache_manager' => true,
            'proxy_client' => [
                'default' => 'noop',
                'noop' => null,
            ],
        ];

        $container->prependExtensionConfig('fos_http_cache', $config);
    }
}

<?php

namespace Kunstmaan\CookieBundle\DependencyInjection;

use Kunstmaan\CookieBundle\Helper\LegalCookieHelper;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;

class KunstmaanCookieExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $container->getDefinition(LegalCookieHelper::class)->setArgument(2, $config['consent_cookie']['lifetime']);
    }
}

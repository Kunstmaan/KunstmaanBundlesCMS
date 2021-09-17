<?php

namespace Kunstmaan\AdminBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Container extension to bridge the configuration from WhiteOctoberPagerfantaBundle to BabDevPagerfantaBundle
 * NEXT_MAJOR remove class
 *
 * @deprecated since KunstmaanAdminBundle 5.9. Migrate your Pagerfanta configuration from WhiteOctoberPagerfantaBundle to BabDevPagerfantaBundle, the configuration bridge will be removed in KunstmaanAdminBundle 6.0.
 *
 * @internal
 */
class PagerfantaExtension extends Extension implements PrependExtensionInterface
{
    public function getAlias(): string
    {
        return 'white_october_pagerfanta';
    }

    public function getConfiguration(array $config, ContainerBuilder $container): PagerfantaConfiguration
    {
        return new PagerfantaConfiguration();
    }

    public function load(array $config, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration($config, $container), $config);

        $container->setParameter('white_october_pagerfanta.default_view', $config['default_view']);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $container->getExtensionConfig($this->getAlias()));

        $container->prependExtensionConfig('babdev_pagerfanta', $config);
    }
}

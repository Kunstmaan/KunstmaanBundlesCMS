<?php

namespace Kunstmaan\UtilitiesBundle\DependencyInjection;

use Kunstmaan\UtilitiesBundle\Helper\Slugifier;
use Kunstmaan\UtilitiesBundle\Helper\SlugifierInterface;
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
class KunstmaanUtilitiesExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('commands.yml');
        $loader->load('services.yml');

        $container->setAlias(SlugifierInterface::class, new Alias(Slugifier::class));
    }
}

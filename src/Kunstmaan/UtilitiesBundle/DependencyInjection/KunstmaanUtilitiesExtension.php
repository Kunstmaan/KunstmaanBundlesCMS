<?php

namespace Kunstmaan\UtilitiesBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class KunstmaanUtilitiesExtension extends Extension
{
    /**
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if ($container->hasParameter('secret')) {
            $container->setParameter('kunstmaan_utilities.cipher.secret', $container->getParameter('secret'));
        } else {
            $container->setParameter('kunstmaan_utilities.cipher.secret', $config['cipher']['secret']);
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('commands.yml');
        $loader->load('services.yml');
    }
}

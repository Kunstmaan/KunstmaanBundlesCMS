<?php

namespace Kunstmaan\UtilitiesBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class KunstmaanUtilitiesExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if ($container->hasParameter('kunstmaan_utilities.cipher.secret')) {
            @trigger_error('Setting the "kunstmaan_utilities.cipher.secret" parameter is deprecated since KunstmaanUtilitiesBundle 5.2, this value will be ignored/overwritten in KunstmaanUtilitiesBundle 6.0. Use the "kunstmaan_utilities.cipher.secret" config instead if you want to set a different value than the default "%kernel.secret%".', E_USER_DEPRECATED);
        } elseif ($container->hasParameter('secret')) {
            $container->setParameter('kunstmaan_utilities.cipher.secret', $container->getParameter('secret'));
        } else {
            $container->setParameter('kunstmaan_utilities.cipher.secret', $config['cipher']['secret']);
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('commands.yml');
        $loader->load('services.yml');
    }
}

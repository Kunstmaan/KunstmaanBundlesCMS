<?php

namespace Kunstmaan\AdminListBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KunstmaanAdminListExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $container->setParameter('kunstmaan_entity.lock_check_interval', $config['lock']['check_interval']);
        $container->setParameter('kunstmaan_entity.lock_threshold', $config['lock']['threshold']);
        $container->setParameter('kunstmaan_entity.lock_enabled', $config['lock']['enabled']);

        $loader->load('services.yml');
    }

    public function prepend(ContainerBuilder $container)
    {
        $parameterName = 'datePicker_startDate';

        $config = array();
        $config['globals'][$parameterName] = '01/01/1970';

        if ($container->hasParameter($parameterName)) {
            $config['globals'][$parameterName] = $container->getParameter($parameterName);
        }

        $container->prependExtensionConfig('twig', $config);
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);
    }
}

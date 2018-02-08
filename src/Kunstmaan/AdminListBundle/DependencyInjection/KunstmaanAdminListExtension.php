<?php

namespace Kunstmaan\AdminListBundle\DependencyInjection;

use Kunstmaan\AdminListBundle\AdminList\AdminListFactory;
use Kunstmaan\AdminListBundle\EventSubscriber\AdminListSubscriber;
use Kunstmaan\AdminListBundle\Service\EntityVersionLockService;
use Kunstmaan\AdminListBundle\Service\ExportService;
use Kunstmaan\AdminListBundle\Twig\AdminListTwigExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
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
     * {@inheritDoc}
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

        // === BEGIN ALIASES ====
        $container->addAliases(
            [
                'kunstmaan_adminlist.factory' => new Alias(AdminListFactory::class),
                'kunstmaan_adminlist.service.export' => new Alias(ExportService::class),
                'kunstmaan_adminlist.twig.extension' => new Alias(AdminListTwigExtension::class),
                'kunstmaan_entity.admin_entity.entity_version_lock_service' => new Alias(EntityVersionLockService::class),
                'kunstmaan_adminlist.subscriber.adminlist' => new Alias(AdminListSubscriber::class),
            ]
        );

        $this->addParameteredAliases(
            $container,
            [
                ['kunstmaan_adminlist.service.export.class', ExportService::class, true],
            ]
        );
        // === END ALIASES ====
    }

    /**
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {

        $parameterName = 'datePicker_startDate';

        $config = [];
        $config['globals'][$parameterName] = '01/01/1970';

        if ($container->hasParameter($parameterName)) {
            $config['globals'][$parameterName] = $container->getParameter($parameterName);
        }

        $container->prependExtensionConfig('twig', $config);
        $configs = $container->getExtensionConfig($this->getAlias());
        $this->processConfiguration(new Configuration(), $configs);
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

<?php

namespace Kunstmaan\AdminListBundle\DependencyInjection\Compiler;

use Kunstmaan\AdminListBundle\AdminList\AdminListFactory;
use Kunstmaan\AdminListBundle\EventSubscriber\AdminListSubscriber;
use Kunstmaan\AdminListBundle\Service\EntityVersionLockService;
use Kunstmaan\AdminListBundle\Service\ExportService;
use Kunstmaan\AdminListBundle\Twig\AdminListTwigExtension;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class DeprecationsCompilerPass
 *
 * @package Kunstmaan\AdminListBundle\DependencyInjection\Compiler
 */
class DeprecationsCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->addDeprecatedChildDefinitions(
            $container,
            [
                ['kunstmaan_adminlist.factory', AdminListFactory::class],
                ['kunstmaan_adminlist.service.export', ExportService::class],
                ['kunstmaan_adminlist.twig.extension', AdminListTwigExtension::class],
                ['kunstmaan_entity.admin_entity.entity_version_lock_service', EntityVersionLockService::class],
                ['kunstmaan_adminlist.subscriber.adminlist', AdminListSubscriber::class],
            ]
        );

        $this->addDeprecatedChildDefinitions(
            $container,
            [
                ['kunstmaan_adminlist.service.export.class', ExportService::class],
            ],
            true
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $deprecations
     * @param bool             $parametered
     */
    private function addDeprecatedChildDefinitions(ContainerBuilder $container, array $deprecations, $parametered = false)
    {
        foreach ($deprecations as $deprecation) {
            // Don't allow service with same name as class.
            if ($parametered && $container->getParameter($deprecation[0]) === $deprecation[1]) {
                continue;
            }

            $definition = new ChildDefinition($deprecation[1]);
            if (isset($deprecation[2])) {
                $definition->setPublic($deprecation[2]);
            }

            if ($parametered) {
                $class = $container->getParameter($deprecation[0]);
                $definition->setClass($class);
                $definition->setDeprecated(
                    true,
                    'Override service class with "%service_id%" is deprecated since KunstmaanAdminListBundle 5.1 and will be removed in 6.0. Override the service instance instead.'
                );
                $container->setDefinition($class, $definition);
            } else {
                $definition->setClass($deprecation[1]);
                $definition->setDeprecated(
                    true,
                    'Passing a "%service_id%" instance is deprecated since KunstmaanAdminListBundle 5.1 and will be removed in 6.0. Use the FQCN instead.'
                );
                $container->setDefinition($deprecation[0], $definition);
            }
        }
    }
}

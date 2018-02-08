<?php

namespace Kunstmaan\MenuBundle\DependencyInjection\Compiler;

use Kunstmaan\MenuBundle\Repository\MenuItemRepository;
use Kunstmaan\MenuBundle\Service\MenuAdaptor;
use Kunstmaan\MenuBundle\Service\MenuService;
use Kunstmaan\MenuBundle\Service\RenderService;
use Kunstmaan\MenuBundle\Twig\MenuTwigExtension;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class DeprecationsCompilerPass
 *
 * @package Kunstmaan\MenuBundle\DependencyInjection\Compiler
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
                ['kunstmaan_menu.menu.adaptor', MenuAdaptor::class],
                ['kunstmaan_menu.menu.service', MenuService::class],
                ['kunstmaan_menu.menu.render_service', RenderService::class],
                ['kunstmaan_menu.menu.repository', MenuItemRepository::class],
                ['kunstmaan_menu.menu.twig.extension', MenuTwigExtension::class],
            ]
        );

        $this->addDeprecatedChildDefinitions(
            $container,
            [
                ['kunstmaan_menu.menu.adaptor.class', MenuAdaptor::class],
                ['kunstmaan_menu.menu.service.class', MenuService::class],
                ['kunstmaan_menu.menu.twig.extension.class', MenuTwigExtension::class],
                ['kunstmaan_menu.menu.repository.class', MenuItemRepository::class],
                ['kunstmaan_menu.menu.render_service.class', RenderService::class],
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
                    'Override service class with "%service_id%" is deprecated since KunstmaanMenuBundle 5.1 and will be removed in 6.0. Override the service instance instead.'
                );
                $container->setDefinition($class, $definition);
            } else {
                $definition->setClass($deprecation[1]);
                $definition->setDeprecated(
                    true,
                    'Passing a "%service_id%" instance is deprecated since KunstmaanMenuBundle 5.1 and will be removed in 6.0. Use the FQCN instead.'
                );
                $container->setDefinition($deprecation[0], $definition);
            }
        }
    }
}

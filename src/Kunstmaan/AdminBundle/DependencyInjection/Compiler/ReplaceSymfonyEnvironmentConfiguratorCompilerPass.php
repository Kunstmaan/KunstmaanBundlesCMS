<?php

declare(strict_types=1);

namespace Kunstmaan\AdminBundle\DependencyInjection\Compiler;

use Kunstmaan\AdminBundle\Twig\Configurator\EnvironmentConfigurator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * NEXT_MAJOR Remove compiler pass when groundcontrol setup is removed and webpack encore is the default
 *
 * @internal
 */
final class ReplaceSymfonyEnvironmentConfiguratorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('twig.configurator.environment')) {
            return;
        }

        $definitionCopy = clone $container->getDefinition('twig.configurator.environment');

        $definitionCopy->setClass(EnvironmentConfigurator::class);
        $container->setDefinition('kunstmaan_admin.twig.configurator.environment', $definitionCopy);

        $container->setAlias('twig.configurator.environment', 'kunstmaan_admin.twig.configurator.environment');
    }
}

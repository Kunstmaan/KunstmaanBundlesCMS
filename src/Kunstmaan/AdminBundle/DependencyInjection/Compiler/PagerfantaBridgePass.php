<?php

declare(strict_types=1);

namespace Kunstmaan\AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Compiler pass to bridge the configuration from WhiteOctoberPagerfantaBundle to BabDevPagerfantaBundle
 * NEXT_MAJOR remove class
 *
 * @deprecated since KunstmaanAdminBundle 5.9. Migrate your Pagerfanta configuration from WhiteOctoberPagerfantaBundle to BabDevPagerfantaBundle, the configuration bridge will be removed in KunstmaanAdminBundle 6.0.
 *
 * @internal
 */
final class PagerfantaBridgePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $this->changeViewFactoryClass($container);
        $this->aliasRenamedServices($container);
    }

    private function changeViewFactoryClass(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('white_october_pagerfanta.view_factory.class') || !$container->hasDefinition('pagerfanta.view_factory')) {
            return;
        }

        $container->getDefinition('pagerfanta.view_factory')
            ->setClass($container->getParameter('white_october_pagerfanta.view_factory.class'));
    }

    private function aliasRenamedServices(ContainerBuilder $container): void
    {
        if ($container->hasDefinition('pagerfanta.twig_extension')) {
            $alias = $container->setAlias('twig.extension.pagerfanta', 'pagerfanta.twig_extension');
            if (method_exists(Alias::class, 'setDeprecated')) {
                $alias->setDeprecated(true, 'The "%alias_id%" service alias is deprecated since KunstmaanAdminBundle 5.9, use the "pagerfanta.twig_extension" service ID instead.');
            }
        }

        if ($container->hasDefinition('pagerfanta.view_factory')) {
            $alias = $container->setAlias('white_october_pagerfanta.view_factory', 'pagerfanta.view_factory');
            if (method_exists(Alias::class, 'setDeprecated')) {
                $alias->setDeprecated(true, 'The "%alias_id%" service alias is deprecated since KunstmaanAdminBundle 5.9, use the "pagerfanta.view_factory" service ID instead.');
            }
        }
    }
}

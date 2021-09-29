<?php

declare(strict_types=1);

namespace Kunstmaan\NodeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @internal
 */
final class PageRenderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $renderServices = [];
        foreach ($container->findTaggedServiceIds('kunstmaan.node.page_view_data_provider') as $id => $tags) {
            $renderServices[$id] = new Reference($id);
        }

        $container->getDefinition('kunstmaan.view_data_provider_locator')->setArguments([$renderServices]);
    }
}

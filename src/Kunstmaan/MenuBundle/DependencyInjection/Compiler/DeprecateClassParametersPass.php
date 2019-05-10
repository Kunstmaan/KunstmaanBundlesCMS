<?php

namespace Kunstmaan\MenuBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @internal
 */
final class DeprecateClassParametersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $expectedValues = [
            'kunstmaan_menu.menu.adaptor.class' => \Kunstmaan\MenuBundle\Service\MenuAdaptor::class,
            'kunstmaan_menu.menu.service.class' => \Kunstmaan\MenuBundle\Service\MenuService::class,
            'kunstmaan_menu.menu.twig.extension.class' => \Kunstmaan\MenuBundle\Twig\MenuTwigExtension::class,
            'kunstmaan_menu.menu.repository.class' => \Kunstmaan\MenuBundle\Repository\MenuItemRepository::class,
            'kunstmaan_menu.menu.render_service.class' => \Kunstmaan\MenuBundle\Service\RenderService::class,
        ];

        foreach ($expectedValues as $parameter => $expectedValue) {
            if (false === $container->hasParameter($parameter)) {
                continue;
            }

            $currentValue = $container->getParameter($parameter);
            if ($currentValue !== $expectedValue) {
                @trigger_error(sprintf('Using the "%s" parameter to change the class of the service definition is deprecated in KunstmaanMenuBundle 5.2 and will be removed in KunstmaanMenuBundle 6.0. Use service decoration or a service alias instead.', $parameter), E_USER_DEPRECATED);
            }
        }
    }
}

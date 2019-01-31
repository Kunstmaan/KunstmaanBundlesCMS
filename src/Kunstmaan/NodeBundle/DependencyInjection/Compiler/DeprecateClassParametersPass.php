<?php

namespace Kunstmaan\NodeBundle\DependencyInjection\Compiler;

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
            'kunstmaan_node.slugrouter.class' => \Kunstmaan\NodeBundle\Router\SlugRouter::class,
            'kunstmaan_node.sluglistener.class' => \Kunstmaan\NodeBundle\EventListener\SlugListener::class,
            'kunstmaan_node.helper.url.class' => \Kunstmaan\NodeBundle\Helper\URLHelper::class,
            'kunstmaan_node.url_replace.twig.class' => \Kunstmaan\NodeBundle\Twig\UrlReplaceTwigExtension::class,
            'kunstmaan_multi_domain.url_replace.controller.class' => \Kunstmaan\NodeBundle\Controller\UrlReplaceController::class,
            'kunstmaan_node.toolbar.collector.node.class' => \Kunstmaan\NodeBundle\Toolbar\NodeDataCollector::class,
        ];

        foreach ($expectedValues as $parameter => $expectedValue) {
            if (false === $container->hasParameter($parameter)) {
                continue;
            }

            $currentValue = $container->getParameter($parameter);
            if ($currentValue !== $expectedValue) {
                @trigger_error(sprintf('Using the "%s" parameter to change the class of the service definition is deprecated in KunstmaanNodeBundle 5.2 and will be removed in KunstmaanNodeBundle 6.0. Use service decoration or a service alias instead.', $parameter), E_USER_DEPRECATED);
            }
        }
    }
}

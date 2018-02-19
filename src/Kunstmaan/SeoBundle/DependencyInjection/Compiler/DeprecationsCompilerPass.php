<?php

namespace Kunstmaan\SeoBundle\DependencyInjection\Compiler;

use Kunstmaan\SeoBundle\EventListener\CloneListener;
use Kunstmaan\SeoBundle\EventListener\NodeListener;
use Kunstmaan\SeoBundle\Helper\Menu\SeoManagementMenuAdaptor;
use Kunstmaan\SeoBundle\Helper\OrderConverter;
use Kunstmaan\SeoBundle\Helper\OrderPreparer;
use Kunstmaan\SeoBundle\Twig\GoogleAnalyticsTwigExtension;
use Kunstmaan\SeoBundle\Twig\SeoTwigExtension;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class DeprecationsCompilerPass
 *
 * @package Kunstmaan\SeoBundle\DependencyInjection\Compiler
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
                ['kunstmaan_seo.twig.extension', SeoTwigExtension::class],
                ['kunstmaan_seo.google_analytics.order_preparer', OrderPreparer::class],
                ['kunstmaan_seo.google_analytics.order_converter', OrderConverter::class],
                ['kunstmaan_seo.google_analytics.twig.extension', GoogleAnalyticsTwigExtension::class],
                ['kunstmaan_seo.node.listener', NodeListener::class],
                ['kunstmaan_seo.clone.listener', CloneListener::class],
                ['kunstmaanseobundle.seo_management_menu_adaptor', SeoManagementMenuAdaptor::class],
            ]
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
            $definition = new ChildDefinition($deprecation[1]);
            if (isset($deprecation[2])) {
                $definition->setPublic($deprecation[2]);
            }
            $definition->setClass($deprecation[1]);
            $definition->setDeprecated(
                true,
                'Passing a "%service_id%" instance is deprecated since KunstmaanSeoBundle 5.1 and will be removed in 6.0. Use the FQCN instead.'
            );
            $container->setDefinition($deprecation[0], $definition);
        }
    }
}

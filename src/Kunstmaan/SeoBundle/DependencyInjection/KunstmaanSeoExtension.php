<?php

namespace Kunstmaan\SeoBundle\DependencyInjection;

use Kunstmaan\SeoBundle\EventListener\CloneListener;
use Kunstmaan\SeoBundle\EventListener\NodeListener;
use Kunstmaan\SeoBundle\Helper\Menu\SeoManagementMenuAdaptor;
use Kunstmaan\SeoBundle\Helper\OrderConverter;
use Kunstmaan\SeoBundle\Helper\OrderPreparer;
use Kunstmaan\SeoBundle\Twig\GoogleAnalyticsTwigExtension;
use Kunstmaan\SeoBundle\Twig\SeoTwigExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;


/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KunstmaanSeoExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('parameters.yml');

        // === BEGIN ALIASES ====
        $container->addAliases(
            [
                'kunstmaan_seo.twig.extension' => new Alias(SeoTwigExtension::class),
                'kunstmaan_seo.google_analytics.order_preparer' => new Alias(OrderPreparer::class),
                'kunstmaan_seo.google_analytics.order_converter' => new Alias(OrderConverter::class),
                'kunstmaan_seo.google_analytics.twig.extension' => new Alias(GoogleAnalyticsTwigExtension::class),
                'kunstmaan_seo.node.listener' => new Alias(NodeListener::class),
                'kunstmaan_seo.clone.listener' => new Alias(CloneListener::class),
                'kunstmaanseobundle.seo_management_menu_adaptor' => new Alias(SeoManagementMenuAdaptor::class),
            ]
        );
        // === END ALIASES ====
    }

    public function prepend(ContainerBuilder $container)
    {
        $liipConfig = Yaml::parse(file_get_contents(__DIR__.'/../Resources/config/imagine_filters.yml'));
        $container->prependExtensionConfig('liip_imagine', $liipConfig['liip_imagine']);

        $configs = $container->getExtensionConfig($this->getAlias());
        $this->processConfiguration(new Configuration(), $configs);
    }
}

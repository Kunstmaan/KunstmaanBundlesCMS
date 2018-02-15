<?php

namespace Kunstmaan\NodeBundle\DependencyInjection;

use Kunstmaan\NodeBundle\Helper\PagesConfiguration;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KunstmaanNodeExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $container->setParameter(
            'twig.form.resources',
            array_merge(
                $container->getParameter('twig.form.resources'),
                ['KunstmaanNodeBundle:Form:formWidgets.html.twig']
            )
        );

        $container->setParameter('kunstmaan_node.show_add_homepage', $config['show_add_homepage']);
        $container->setParameter('kunstmaan_node.lock_check_interval', $config['lock']['check_interval']);
        $container->setParameter('kunstmaan_node.lock_threshold', $config['lock']['threshold']);
        $container->setParameter('kunstmaan_node.lock_enabled', $config['lock']['enabled']);

        $loader->load('services.yml');

        $container->getDefinition(PagesConfiguration::class)->setArguments([$config['pages']]);
    }

    public function prepend(ContainerBuilder $container)
    {
        $cmfRoutingExtraConfig['chain']['routers_by_id']['router.default'] = 100;
        $cmfRoutingExtraConfig['chain']['replace_symfony_router'] = true;
        $container->prependExtensionConfig('cmf_routing', $cmfRoutingExtraConfig);

        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);

        // set twig global params
        $twigConfig['globals']['nodebundleisactive'] = true;
        $twigConfig['globals']['publish_later_stepping'] = $config['publish_later_stepping'];
        $twigConfig['globals']['unpublish_later_stepping'] = $config['unpublish_later_stepping'];
        $container->prependExtensionConfig('twig', $twigConfig);
    }
}

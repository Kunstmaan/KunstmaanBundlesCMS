<?php

namespace Kunstmaan\NodeBundle\DependencyInjection;

use Kunstmaan\NodeBundle\Entity\PageViewDataProviderInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;

class KunstmaanNodeExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $container->setParameter('twig.form.resources', array_merge(
            $container->getParameter('twig.form.resources'),
            ['@KunstmaanNode/Form/formWidgets.html.twig']
        ));

        $container->registerForAutoconfiguration(PageViewDataProviderInterface::class)
            ->addTag('kunstmaan.node.page_view_data_provider')
        ;

        $nodePagesDefinition = new Definition('Kunstmaan\NodeBundle\Helper\PagesConfiguration', [$config['pages']]);
        $nodePagesDefinition->setPublic(true);
        $container->setDefinition('kunstmaan_node.pages_configuration', $nodePagesDefinition);

        $container->setParameter('kunstmaan_node.permissions.enabled', $config['enable_permissions']);
        $container->setParameter('kunstmaan_node.show_add_homepage', $config['show_add_homepage']);
        $container->setParameter('kunstmaan_node.show_duplicate_with_children', $config['show_duplicate_with_children']);
        $container->setParameter('kunstmaan_node.enable_export_page_template', $config['enable_export_page_template']);
        $container->setParameter('kunstmaan_node.lock_check_interval', $config['lock']['check_interval']);
        $container->setParameter('kunstmaan_node.lock_threshold', $config['lock']['threshold']);
        $container->setParameter('kunstmaan_node.lock_enabled', $config['lock']['enabled']);

        $loader->load('services.yml');
        $loader->load('commands.yml');
    }

    public function prepend(ContainerBuilder $container): void
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

        $stofDoctrineExtensionsConfig = [
            'orm' => [
                'default' => [
                    'tree' => true,
                ],
            ],
        ];

        $container->prependExtensionConfig('stof_doctrine_extensions', $stofDoctrineExtensionsConfig);
    }
}

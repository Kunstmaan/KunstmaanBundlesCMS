<?php

namespace Kunstmaan\NodeBundle\DependencyInjection;

use Kunstmaan\NodeBundle\Controller\UrlReplaceController;
use Kunstmaan\NodeBundle\EventListener\FixDateListener;
use Kunstmaan\NodeBundle\EventListener\LogPageEventsSubscriber;
use Kunstmaan\NodeBundle\EventListener\MappingListener;
use Kunstmaan\NodeBundle\EventListener\NodeListener;
use Kunstmaan\NodeBundle\EventListener\NodeTranslationListener;
use Kunstmaan\NodeBundle\EventListener\RenderContextListener;
use Kunstmaan\NodeBundle\EventListener\SlugListener;
use Kunstmaan\NodeBundle\EventListener\SlugSecurityListener;
use Kunstmaan\NodeBundle\Form\NodeChoiceType;
use Kunstmaan\NodeBundle\Form\Type\SlugType;
use Kunstmaan\NodeBundle\Form\Type\URLChooserType;
use Kunstmaan\NodeBundle\Helper\Menu\ActionsMenuBuilder;
use Kunstmaan\NodeBundle\Helper\Menu\PageMenuAdaptor;
use Kunstmaan\NodeBundle\Helper\NodeAdmin\NodeAdminPublisher;
use Kunstmaan\NodeBundle\Helper\NodeAdmin\NodeVersionLockHelper;
use Kunstmaan\NodeBundle\Helper\NodeMenu;
use Kunstmaan\NodeBundle\Helper\PagesConfiguration;
use Kunstmaan\NodeBundle\Helper\Services\ACLPermissionCreatorService;
use Kunstmaan\NodeBundle\Helper\Services\PageCreatorService;
use Kunstmaan\NodeBundle\Helper\URLHelper;
use Kunstmaan\NodeBundle\Router\SlugRouter;
use Kunstmaan\NodeBundle\Toolbar\NodeDataCollector;
use Kunstmaan\NodeBundle\Twig\NodeTwigExtension;
use Kunstmaan\NodeBundle\Twig\PagesConfigurationTwigExtension;
use Kunstmaan\NodeBundle\Twig\UrlReplaceTwigExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
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

        // === BEGIN ALIASES ====
        $container->addAliases(
            [
                'kunstmaan_node.nodetranslation.listener' => new Alias(NodeTranslationListener::class),
                'kunstmaan_node.menu.adaptor.pages' => new Alias(PageMenuAdaptor::class),
                'kunstmaan_node.form.type.urlchooser' => new Alias(URLChooserType::class),
                'kunstmaan_node.form.type.slug' => new Alias(SlugType::class),
                'kunstmaan_node.form.type.nodechoice' => new Alias(NodeChoiceType::class),
                'kunstmaan_node.admin_node.publisher' => new Alias(NodeAdminPublisher::class),
                'kunstmaan_node.admin_node.node_version_lock_helper' => new Alias(NodeVersionLockHelper::class),
                'kunstmaan_node.actions_menu_builder' => new Alias(ActionsMenuBuilder::class),
                'kunstmaan_node.fix_date.listener' => new Alias(FixDateListener::class),
                'kunstmaan_node.edit_node.listener' => new Alias(NodeListener::class),
                'kunstmaan_node.log_page_events.subscriber' => new Alias(LogPageEventsSubscriber::class),
                'kunstmaan_node.slugrouter' => new Alias(SlugRouter::class),
                'kunstmaan_node.pages_configuration.twig_extension' => new Alias(PagesConfigurationTwigExtension::class, false),
                'kunstmaan_node.url_replace.twig.extension' => new Alias(UrlReplaceTwigExtension::class),
                'kunstmaan_node.page_creator_service' => new Alias(PageCreatorService::class),
                'kunstmaan_node.acl_permission_creator_service' => new Alias(ACLPermissionCreatorService::class),
                'kunstmaan_node.doctrine_mapping.listener' => new Alias(MappingListener::class),
                'kunstmaan_node.slug.listener' => new Alias(SlugListener::class),
                'kunstmaan_node.slug.security.listener' => new Alias(SlugSecurityListener::class),
                'kunstmaan_node.render.context.listener' => new Alias(RenderContextListener::class),
                'kunstmaan_node.node_menu' => new Alias(NodeMenu::class),
                'kunstmaan_node.node.twig.extension' => new Alias(NodeTwigExtension::class),
                'kunstmaan_node.helper.url' => new Alias(URLHelper::class),
                'kunstmaan_node.url_replace.controller' => new Alias(UrlReplaceController::class),
                'kunstmaan_node.datacollector.node' => new Alias(NodeDataCollector::class),
                'kunstmaan_node.pages_configuration' => new Alias(PagesConfiguration::class),
            ]
        );

        $this->addParameteredAliases(
            $container,
            [
                ['kunstmaan_node.slugrouter.class', SlugRouter::class, true],
                ['kunstmaan_node.url_replace.twig.class', UrlReplaceTwigExtension::class, true],
                ['kunstmaan_node.sluglistener.class', SlugListener::class, true],
                ['kunstmaan_node.helper.url.class', URLHelper::class, true],
                ['kunstmaan_multi_domain.url_replace.controller.class', UrlReplaceController::class, true],
                ['kunstmaan_node.toolbar.collector.node.class', NodeDataCollector::class, true],
            ]
        );
        // === END ALIASES ====
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

    /**
     * @param ContainerBuilder $container
     * @param array            $aliases
     */
    private function addParameteredAliases(ContainerBuilder $container, $aliases)
    {
        foreach ($aliases as $alias) {
            // Don't allow service with same name as class.
            if ($container->getParameter($alias[0]) !== $alias[1]) {
                $container->setAlias(
                    $container->getParameter($alias[0]),
                    new Alias($alias[1], $alias[2])
                );
            }
        }
    }
}

<?php

namespace Kunstmaan\NodeBundle\DependencyInjection\Compiler;

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
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class DeprecationsCompilerPass
 *
 * @package Kunstmaan\NodeBundle\DependencyInjection\Compiler
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
                ['kunstmaan_node.nodetranslation.listener', NodeTranslationListener::class],
                ['kunstmaan_node.menu.adaptor.pages', PageMenuAdaptor::class],
                ['kunstmaan_node.form.type.urlchooser', URLChooserType::class],
                ['kunstmaan_node.form.type.slug', SlugType::class],
                ['kunstmaan_node.form.type.nodechoice', NodeChoiceType::class],
                ['kunstmaan_node.admin_node.publisher', NodeAdminPublisher::class],
                ['kunstmaan_node.admin_node.node_version_lock_helper', NodeVersionLockHelper::class],
                ['kunstmaan_node.actions_menu_builder', ActionsMenuBuilder::class],
                ['kunstmaan_node.fix_date.listener', FixDateListener::class],
                ['kunstmaan_node.edit_node.listener', NodeListener::class],
                ['kunstmaan_node.log_page_events.subscriber', LogPageEventsSubscriber::class],
                ['kunstmaan_node.slugrouter', SlugRouter::class],
                ['kunstmaan_node.pages_configuration.twig_extension', PagesConfigurationTwigExtension::class, false],
                ['kunstmaan_node.url_replace.twig.extension', UrlReplaceTwigExtension::class],
                ['kunstmaan_node.page_creator_service', PageCreatorService::class],
                ['kunstmaan_node.acl_permission_creator_service', ACLPermissionCreatorService::class],
                ['kunstmaan_node.doctrine_mapping.listener', MappingListener::class],
                ['kunstmaan_node.slug.listener', SlugListener::class],
                ['kunstmaan_node.slug.security.listener', SlugSecurityListener::class],
                ['kunstmaan_node.render.context.listener', RenderContextListener::class],
                ['kunstmaan_node.node_menu', NodeMenu::class],
                ['kunstmaan_node.node.twig.extension', NodeTwigExtension::class],
                ['kunstmaan_node.helper.url', URLHelper::class],
                ['kunstmaan_node.url_replace.controller', UrlReplaceController::class],
                ['kunstmaan_node.datacollector.node', NodeDataCollector::class],
                ['kunstmaan_node.pages_configuration', PagesConfiguration::class],
            ]
        );

        $this->addDeprecatedChildDefinitions(
            $container,
            [
                ['kunstmaan_node.slugrouter.class', SlugRouter::class],
                ['kunstmaan_node.url_replace.twig.class', UrlReplaceTwigExtension::class],
                ['kunstmaan_node.sluglistener.class', SlugListener::class],
                ['kunstmaan_node.helper.url.class', URLHelper::class],
                ['kunstmaan_multi_domain.url_replace.controller.class', UrlReplaceController::class],
                ['kunstmaan_node.toolbar.collector.node.class', NodeDataCollector::class],
            ],
            true
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
            // Don't allow service with same name as class.
            if ($parametered && $container->getParameter($deprecation[0]) === $deprecation[1]) {
                continue;
            }

            $definition = new ChildDefinition($deprecation[1]);
            if (isset($deprecation[2])) {
                $definition->setPublic($deprecation[2]);
            }

            if ($parametered) {
                $class = $container->getParameter($deprecation[0]);
                $definition->setClass($class);
                $definition->setDeprecated(
                    true,
                    'Override service class with "%service_id%" is deprecated since KunstmaanNodeBundle 5.1 and will be removed in 6.0. Override the service instance instead.'
                );
                $container->setDefinition($class, $definition);
            } else {
                $definition->setClass($deprecation[1]);
                $definition->setDeprecated(
                    true,
                    'Passing a "%service_id%" instance is deprecated since KunstmaanNodeBundle 5.1 and will be removed in 6.0. Use the FQCN instead.'
                );
                $container->setDefinition($deprecation[0], $definition);
            }
        }
    }
}

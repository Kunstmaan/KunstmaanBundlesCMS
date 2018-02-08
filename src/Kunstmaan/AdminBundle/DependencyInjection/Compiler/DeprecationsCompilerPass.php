<?php

namespace Kunstmaan\AdminBundle\DependencyInjection\Compiler;

use Kunstmaan\AdminBundle\EventListener\AdminLocaleListener;
use Kunstmaan\AdminBundle\EventListener\CloneListener;
use Kunstmaan\AdminBundle\EventListener\ExceptionSubscriber;
use Kunstmaan\AdminBundle\EventListener\LoginListener;
use Kunstmaan\AdminBundle\EventListener\MappingListener;
use Kunstmaan\AdminBundle\EventListener\PasswordCheckListener;
use Kunstmaan\AdminBundle\EventListener\PasswordResettingListener;
use Kunstmaan\AdminBundle\EventListener\SessionSecurityListener;
use Kunstmaan\AdminBundle\EventListener\ToolbarListener;
use Kunstmaan\AdminBundle\Form\ColorType;
use Kunstmaan\AdminBundle\Form\RangeType;
use Kunstmaan\AdminBundle\Form\WysiwygType;
use Kunstmaan\AdminBundle\Helper\AdminPanel\AdminPanel;
use Kunstmaan\AdminBundle\Helper\AdminPanel\DefaultAdminPanelAdaptor;
use Kunstmaan\AdminBundle\Helper\AdminRouteHelper;
use Kunstmaan\AdminBundle\Helper\CloneHelper;
use Kunstmaan\AdminBundle\Helper\Creators\ACLPermissionCreator;
use Kunstmaan\AdminBundle\Helper\DomainConfiguration;
use Kunstmaan\AdminBundle\Helper\FormHelper;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\ModulesMenuAdaptor;
use Kunstmaan\AdminBundle\Helper\Menu\SettingsMenuAdaptor;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclNativeHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionAdmin;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Voter\AclVoter;
use Kunstmaan\AdminBundle\Helper\Security\OAuth\OAuthUserCreator;
use Kunstmaan\AdminBundle\Helper\Security\OAuth\OAuthUserFinder;
use Kunstmaan\AdminBundle\Helper\Toolbar\DataCollector;
use Kunstmaan\AdminBundle\Helper\UserProcessor;
use Kunstmaan\AdminBundle\Helper\VersionCheck\VersionChecker;
use Kunstmaan\AdminBundle\Security\OAuthAuthenticator;
use Kunstmaan\AdminBundle\Toolbar\BundleVersionDataCollector;
use Kunstmaan\AdminBundle\Toolbar\ExceptionDataCollector;
use Kunstmaan\AdminBundle\Twig\AdminPermissionsTwigExtension;
use Kunstmaan\AdminBundle\Twig\AdminRouteHelperTwigExtension;
use Kunstmaan\AdminBundle\Twig\DateByLocaleExtension;
use Kunstmaan\AdminBundle\Twig\FormToolsExtension;
use Kunstmaan\AdminBundle\Twig\GoogleSignInTwigExtension;
use Kunstmaan\AdminBundle\Twig\LocaleSwitcherTwigExtension;
use Kunstmaan\AdminBundle\Twig\MenuTwigExtension;
use Kunstmaan\AdminBundle\Twig\MultiDomainAdminTwigExtension;
use Kunstmaan\AdminBundle\Twig\SidebarTwigExtension;
use Kunstmaan\AdminBundle\Twig\TabsTwigExtension;
use Kunstmaan\AdminBundle\Twig\ToolbarTwigExtension;
use Kunstmaan\AdminBundle\Validator\Constraints\PasswordRestrictionsValidator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class DeprecationsCompilerPass
 *
 * @package Kunstmaan\AdminBundle\DependencyInjection\Compiler
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
                ['kunstmaan_admin.menubuilder', MenuBuilder::class],
                ['kunstmaan_admin.admin_panel', AdminPanel::class],
                ['kunstmaan_admin.menu.adaptor.modules', ModulesMenuAdaptor::class],
                ['kunstmaan_admin.menu.adaptor.settings', SettingsMenuAdaptor::class],
                ['kunstmaan_admin.login.listener', LoginListener::class],
                ['kunstmaan_admin.admin_locale.listener', AdminLocaleListener::class],
                ['kunstmaan_admin.menu.twig.extension', MenuTwigExtension::class],
                ['kunstmaan_admin.localeswitcher.twig.extension', LocaleSwitcherTwigExtension::class],
                ['kunstmaan_admin.multidomain.twig.extension', MultiDomainAdminTwigExtension::class],
                ['kunstmaan_admin.locale.twig.extension', DateByLocaleExtension::class],
                ['kunstmaan_admin.formtools.twig.extension', FormToolsExtension::class],
                ['kunstmaan_admin.permissions.twig.extension', AdminPermissionsTwigExtension::class],
                ['kunstmaan_admin.acl.helper', AclHelper::class],
                ['kunstmaan_admin.acl.native.helper', AclNativeHelper::class],
                ['kunstmaan_admin.security.acl.permission.map', PermissionMap::class],
                ['kunstmaan_admin.security.acl.voter', AclVoter::class, false],
                ['kunstmaan_admin.permissionadmin', PermissionAdmin::class],
                ['kunstmaan_admin.clone.helper', CloneHelper::class],
                ['kunstmaan_admin.adminroute.helper', AdminRouteHelper::class],
                ['kunstmaan_admin.adminroute.twig.extension', AdminRouteHelperTwigExtension::class],
                ['kunstmaan_admin.clone.listener', CloneListener::class],
                ['kunstmaan_admin.logger.processor.user', UserProcessor::class],
                ['kunstmaan_admin.form.helper', FormHelper::class],
                ['kunstmaan_admin.tabs.twig.extension', TabsTwigExtension::class],
                ['kunstmaan_admin.permission_creator', ACLPermissionCreator::class],
                ['kunstmaan_admin.versionchecker', VersionChecker::class],
                ['kunstmaan_admin.form.type.color', ColorType::class],
                ['kunstmaan_admin.doctrine_mapping.listener', MappingListener::class],
                ['kunstmaan_admin.form.type.range', RangeType::class],
                ['kunstmaan_admin.session_security', RangeType::class],
                ['kunstmaan_form.type.wysiwyg', WysiwygType::class],
                ['kunstmaan_admin.password_resetting.listener', WysiwygType::class],
                ['kunstmaan_admin.password_check.listener', WysiwygType::class],
                ['kunstmaan_admin.admin_panel.adaptor', DefaultAdminPanelAdaptor::class],
                ['kunstmaan_admin.domain_configuration', DomainConfiguration::class],
                ['kunstmaan_admin.oauth_authenticator', OAuthAuthenticator::class],
                ['kunstmaan_admin.oauth_user_creator', OAuthUserCreator::class],
                ['kunstmaan_admin.oauth_user_finder', OAuthUserFinder::class],
                ['kunstmaan_admin.google_signin.twig.extension', GoogleSignInTwigExtension::class],
                ['kunstmaan_admin.sidebar.twig.extension', SidebarTwigExtension::class],
                ['kunstmaan_admin.validator.password_restrictions', PasswordRestrictionsValidator::class],
                ['kunstmaan_admin.exception.listener', ExceptionSubscriber::class],
                ['kunstmaan_admin.toolbar.twig.extension', ToolbarTwigExtension::class],
                ['kunstmaan_admin.toolbar.datacollector', DataCollector::class],
                ['kunstmaan_admin.toolbar.listener', ToolbarListener::class],
                ['kunstmaan_admin.datacollector.bundleversion', BundleVersionDataCollector::class],
                ['kunstmaan_admin.datacollector.exception', ExceptionDataCollector::class],
            ]
        );

        $this->addDeprecatedChildDefinitions(
            $container,
            [
                ['kunstmaan_admin.security.acl.permission.map.class', PermissionMap::class],
                ['kunstmaan_admin.menubuilder.class', MenuBuilder::class],
                ['kunstmaan_admin.admin_panel.class', AdminPanel::class],
                ['kunstmaan_admin.login.listener.class', LoginListener::class],
                ['kunstmaan_admin.admin_locale.listener.class', AdminLocaleListener::class],
                ['kunstmaan_admin.acl.helper.class', AclHelper::class],
                ['kunstmaan_admin.acl.native.helper.class', AclNativeHelper::class],
                ['kunstmaan_admin.clone.listener.class', CloneListener::class],
                ['kunstmaan_admin.session_security.class', SessionSecurityListener::class],
                ['kunstmaan_admin.password_resetting.listener.class', PasswordResettingListener::class],
                ['kunstmaan_admin.password_check.listener.class', PasswordCheckListener::class],
                ['kunstmaan_admin.domain_configuration.class', DomainConfiguration::class],
                ['kunstmaan_admin.validator.password_restrictions.class', PasswordRestrictionsValidator::class],
                ['kunstmaan_admin.adminroute.helper.class', AdminRouteHelper::class],
                ['kunstmaan_admin.adminroute.twig.class', AdminRouteHelperTwigExtension::class],
                ['kunstmaan_admin.exception.listener.class', ExceptionSubscriber::class],
                ['kunstmaan_admin.toolbar.listener.class', ToolbarListener::class],
                ['kunstmaan_admin.toolbar.collector.bundle.class', BundleVersionDataCollector::class],
                ['kunstmaan_admin.toolbar.collector.exception.class', ExceptionDataCollector::class],
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
                    'Override service class with "%service_id%" is deprecated since KunstmaanAdminBundle 5.1 and will be removed in 6.0. Override the service instance instead.'
                );
                $container->setDefinition($class, $definition);
            } else {
                $definition->setClass($deprecation[1]);
                $definition->setDeprecated(
                    true,
                    'Passing a "%service_id%" instance is deprecated since KunstmaanAdminBundle 5.1 and will be removed in 6.0. Use the FQCN instead.'
                );
                $container->setDefinition($deprecation[0], $definition);
            }
        }
    }
}

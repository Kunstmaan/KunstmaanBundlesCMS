<?php

namespace Kunstmaan\AdminBundle\DependencyInjection;

use FOS\UserBundle\Form\Type\ResettingFormType;
use InvalidArgumentException;
use Kunstmaan\AdminBundle\Entity\Group;
use Kunstmaan\AdminBundle\Entity\User;
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
use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
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
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;


/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KunstmaanAdminExtension extends Extension implements PrependExtensionInterface
{
    /**
     * Loads a specific configuration.
     *
     * @param array            $configs   An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $container->setParameter('version_checker.url', 'https://bundles.kunstmaan.be/version-check');
        $container->setParameter('version_checker.timeframe', 60 * 60 * 24);
        $container->setParameter('version_checker.enabled', true);

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if (array_key_exists('dashboard_route', $config)) {
            $container->setParameter('kunstmaan_admin.dashboard_route', $config['dashboard_route']);
        }
        if (array_key_exists('admin_password', $config)) {
            $container->setParameter('kunstmaan_admin.admin_password', $config['admin_password']);
        }
        $container->setParameter('kunstmaan_admin.admin_locales', $config['admin_locales']);
        $container->setParameter('kunstmaan_admin.default_admin_locale', $config['default_admin_locale']);

        $container->setParameter('kunstmaan_admin.session_security.ip_check', $config['session_security']['ip_check']);
        $container->setParameter('kunstmaan_admin.session_security.user_agent_check', $config['session_security']['user_agent_check']);

        $container->setParameter('kunstmaan_admin.admin_prefix', $this->normalizeUrlSlice($config['admin_prefix']));

        $container->setParameter('kunstmaan_admin.admin_exception_excludes', $config['admin_exception_excludes']);

        $container->setParameter('kunstmaan_admin.google_signin.enabled', $config['google_signin']['enabled']);
        $container->setParameter('kunstmaan_admin.google_signin.client_id', $config['google_signin']['client_id']);
        $container->setParameter('kunstmaan_admin.google_signin.client_secret', $config['google_signin']['client_secret']);
        $container->setParameter('kunstmaan_admin.google_signin.hosted_domains', $config['google_signin']['hosted_domains']);

        $container->setParameter('kunstmaan_admin.password_restrictions.min_digits', $config['password_restrictions']['min_digits']);
        $container->setParameter('kunstmaan_admin.password_restrictions.min_uppercase', $config['password_restrictions']['min_uppercase']);
        $container->setParameter(
            'kunstmaan_admin.password_restrictions.min_special_characters',
            $config['password_restrictions']['min_special_characters']
        );
        $container->setParameter('kunstmaan_admin.password_restrictions.min_length', $config['password_restrictions']['min_length']);
        $container->setParameter('kunstmaan_admin.password_restrictions.max_length', $config['password_restrictions']['max_length']);
        $container->setParameter('kunstmaan_admin.enable_toolbar_helper', $config['enable_toolbar_helper']);
        $container->setParameter('kunstmaan_admin.provider_keys', $config['provider_keys']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if (!empty($config['enable_console_exception_listener']) && $config['enable_console_exception_listener']) {
            $loader->load('console_listener.yml');
        }

        if (0 !== \count($config['menu_items'])) {
            $this->addSimpleMenuAdaptor($container, $config['menu_items']);
        }

        // === BEGIN ALIASES ====
        $container->addAliases(
            [
                'kunstmaan_admin.menubuilder' => new Alias(MenuBuilder::class),
                'kunstmaan_admin.admin_panel' => new Alias(AdminPanel::class),
                'kunstmaan_admin.menu.adaptor.modules' => new Alias(ModulesMenuAdaptor::class),
                'kunstmaan_admin.menu.adaptor.settings' => new Alias(SettingsMenuAdaptor::class),
                'kunstmaan_admin.login.listener' => new Alias(LoginListener::class),
                'kunstmaan_admin.admin_locale.listener' => new Alias(AdminLocaleListener::class),
                'kunstmaan_admin.menu.twig.extension' => new Alias(MenuTwigExtension::class),
                'kunstmaan_admin.localeswitcher.twig.extension' => new Alias(LocaleSwitcherTwigExtension::class),
                'kunstmaan_admin.multidomain.twig.extension' => new Alias(MultiDomainAdminTwigExtension::class),
                'kunstmaan_admin.locale.twig.extension' => new Alias(DateByLocaleExtension::class),
                'kunstmaan_admin.formtools.twig.extension' => new Alias(FormToolsExtension::class),
                'kunstmaan_admin.permissions.twig.extension' => new Alias(AdminPermissionsTwigExtension::class),
                'kunstmaan_admin.acl.helper' => new Alias(AclHelper::class),
                'kunstmaan_admin.acl.native.helper' => new Alias(AclNativeHelper::class),
                'kunstmaan_admin.security.acl.permission.map' => new Alias(PermissionMap::class),
                'kunstmaan_admin.security.acl.voter' => new Alias(AclVoter::class, false),
                'kunstmaan_admin.permissionadmin' => new Alias(PermissionAdmin::class),
                'kunstmaan_admin.clone.helper' => new Alias(CloneHelper::class),
                'kunstmaan_admin.adminroute.helper' => new Alias(AdminRouteHelper::class),
                'kunstmaan_admin.adminroute.twig.extension' => new Alias(AdminRouteHelperTwigExtension::class),
                'kunstmaan_admin.clone.listener' => new Alias(CloneListener::class),
                'kunstmaan_admin.logger.processor.user' => new Alias(UserProcessor::class),
                'kunstmaan_admin.form.helper' => new Alias(FormHelper::class),
                'kunstmaan_admin.tabs.twig.extension' => new Alias(TabsTwigExtension::class),
                'kunstmaan_admin.permission_creator' => new Alias(ACLPermissionCreator::class),
                'kunstmaan_admin.versionchecker' => new Alias(VersionChecker::class),
                'kunstmaan_admin.form.type.color' => new Alias(ColorType::class),
                'kunstmaan_admin.doctrine_mapping.listener' => new Alias(MappingListener::class),
                'kunstmaan_admin.form.type.range' => new Alias(RangeType::class),
                'kunstmaan_admin.session_security' => new Alias(RangeType::class),
                'kunstmaan_form.type.wysiwyg' => new Alias(WysiwygType::class),
                'kunstmaan_admin.password_resetting.listener' => new Alias(WysiwygType::class),
                'kunstmaan_admin.password_check.listener' => new Alias(WysiwygType::class),
                'kunstmaan_admin.admin_panel.adaptor' => new Alias(DefaultAdminPanelAdaptor::class),
                'kunstmaan_admin.domain_configuration' => new Alias(DomainConfiguration::class),
                'kunstmaan_admin.oauth_authenticator' => new Alias(OAuthAuthenticator::class),
                'kunstmaan_admin.oauth_user_creator' => new Alias(OAuthUserCreator::class),
                'kunstmaan_admin.oauth_user_finder' => new Alias(OAuthUserFinder::class),
                'kunstmaan_admin.google_signin.twig.extension' => new Alias(GoogleSignInTwigExtension::class),
                'kunstmaan_admin.sidebar.twig.extension' => new Alias(SidebarTwigExtension::class),
                'kunstmaan_admin.validator.password_restrictions' => new Alias(PasswordRestrictionsValidator::class),
                'kunstmaan_admin.exception.listener' => new Alias(ExceptionSubscriber::class),
                'kunstmaan_admin.toolbar.twig.extension' => new Alias(ToolbarTwigExtension::class),
                'kunstmaan_admin.toolbar.datacollector' => new Alias(DataCollector::class),
                'kunstmaan_admin.toolbar.listener' => new Alias(ToolbarListener::class),
                'kunstmaan_admin.datacollector.bundleversion' => new Alias(BundleVersionDataCollector::class),
                'kunstmaan_admin.datacollector.exception' => new Alias(ExceptionDataCollector::class),
                DomainConfigurationInterface::class => new Alias(DomainConfiguration::class),
            ]
        );

        $this->addParameteredAliases(
            $container,
            [
                ['kunstmaan_admin.menubuilder.class', MenuBuilder::class, true],
                ['kunstmaan_admin.admin_panel.class', AdminPanel::class, true],
                ['kunstmaan_admin.login.listener.class', LoginListener::class, true],
                ['kunstmaan_admin.admin_locale.listener.class', AdminLocaleListener::class, true],
                ['kunstmaan_admin.acl.helper.class', AclHelper::class, true],
                ['kunstmaan_admin.acl.native.helper.class', AclNativeHelper::class, true],
                ['kunstmaan_admin.security.acl.permission.map.class', PermissionMap::class, true],
                ['kunstmaan_admin.clone.listener.class', CloneListener::class, true],
                ['kunstmaan_admin.session_security.class', SessionSecurityListener::class, true],
                ['kunstmaan_admin.password_resetting.listener.class', PasswordResettingListener::class, true],
                ['kunstmaan_admin.password_check.listener.class', PasswordCheckListener::class, true],
                ['kunstmaan_admin.domain_configuration.class', DomainConfiguration::class, true],
                ['kunstmaan_admin.validator.password_restrictions.class', PasswordRestrictionsValidator::class, true],
                ['kunstmaan_admin.adminroute.helper.class', AdminRouteHelper::class, true],
                ['kunstmaan_admin.adminroute.twig.class', AdminRouteHelperTwigExtension::class, true],
                ['kunstmaan_admin.exception.listener.class', ExceptionSubscriber::class, true],
                ['kunstmaan_admin.toolbar.listener.class', ToolbarListener::class, true],
                ['kunstmaan_admin.toolbar.collector.bundle.class', BundleVersionDataCollector::class, true],
                ['kunstmaan_admin.toolbar.collector.exception.class', ExceptionDataCollector::class, true],
            ]
        );
        // === END ALIASES ====
    }

    /**
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $knpMenuConfig['twig'] = true; // set to false to disable the Twig extension and the TwigRenderer
        $knpMenuConfig['templating'] = false; // if true, enables the helper for PHP templates
        $knpMenuConfig['default_renderer'] = 'twig'; // The renderer to use, list is also available by default
        $container->prependExtensionConfig('knp_menu', $knpMenuConfig);

        $fosUserConfig['db_driver'] = 'orm'; // other valid values are 'mongodb', 'couchdb'
        $fosUserConfig['from_email']['address'] = 'admin@kunstmaan.be';
        $fosUserConfig['from_email']['sender_name'] = 'admin';
        $fosUserConfig['firewall_name'] = 'main';
        $fosUserConfig['user_class'] = User::class;
        $fosUserConfig['group']['group_class'] = Group::class;
        $fosUserConfig['resetting']['token_ttl'] = 86400;
        // Use this node only if you don't want the global email address for the resetting email
        $fosUserConfig['resetting']['email']['from_email']['address'] = 'admin@kunstmaan.be';
        $fosUserConfig['resetting']['email']['from_email']['sender_name'] = 'admin';
        $fosUserConfig['resetting']['email']['template'] = 'FOSUserBundle:Resetting:email.txt.twig';
        $fosUserConfig['resetting']['form']['type'] = ResettingFormType::class;
        $fosUserConfig['resetting']['form']['name'] = 'fos_user_resetting_form';
        $fosUserConfig['resetting']['form']['validation_groups'] = ['ResetPassword'];
        $container->prependExtensionConfig('fos_user', $fosUserConfig);

        $monologConfig['handlers']['main']['type'] = 'rotating_file';
        $monologConfig['handlers']['main']['path'] = sprintf(
            '%s/%s',
            $container->getParameter('kernel.logs_dir'),
            $container->getParameter('kernel.environment')
        );
        $monologConfig['handlers']['main']['level'] = 'debug';
        $container->prependExtensionConfig('monolog', $monologConfig);

        $configs = $container->getExtensionConfig($this->getAlias());
        $this->processConfiguration(new Configuration(), $configs);
    }

    /**
     * {@inheritDoc}
     */
    public function getNamespace()
    {
        return 'http://bundles.kunstmaan.be/schema/dic/admin';
    }

    /**
     * {@inheritDoc}
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/schema';
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $menuItems
     */
    private function addSimpleMenuAdaptor(ContainerBuilder $container, array $menuItems)
    {
        $definition = new Definition(
            'Kunstmaan\AdminBundle\Helper\Menu\SimpleMenuAdaptor', [
                new Reference('security.authorization_checker'),
                $menuItems,
            ]
        );
        $definition->addTag('kunstmaan_admin.menu.adaptor');

        $container->setDefinition('kunstmaan_admin.menu.adaptor.simple', $definition);
    }

    /**
     * @param string $urlSlice
     *
     * @return string
     */
    protected function normalizeUrlSlice($urlSlice)
    {
        /* Get rid of exotic characters that would break the url */
        $urlSlice = filter_var($urlSlice, FILTER_SANITIZE_URL);

        /* Remove leading and trailing slashes */
        $urlSlice = trim($urlSlice, '/');

        /* Make sure our $urlSlice is literally used in our regex */
        $urlSlice = preg_quote($urlSlice);

        return $urlSlice;
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

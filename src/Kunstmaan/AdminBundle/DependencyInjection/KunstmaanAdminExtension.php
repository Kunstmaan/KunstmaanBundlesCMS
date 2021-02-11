<?php

namespace Kunstmaan\AdminBundle\DependencyInjection;

use FOS\UserBundle\Form\Type\ResettingFormType;
use InvalidArgumentException;
use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

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
        $container->setParameter('version_checker.url', 'https://cms.kunstmaan.be/version-check');
        $container->setParameter('version_checker.timeframe', 60 * 60 * 24);
        $container->setParameter('version_checker.enabled', true);

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if (\array_key_exists('dashboard_route', $config)) {
            $container->setParameter('kunstmaan_admin.dashboard_route', $config['dashboard_route']);
        }
        if (\array_key_exists('admin_password', $config)) {
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
        $container->setParameter('kunstmaan_admin.password_restrictions.min_special_characters', $config['password_restrictions']['min_special_characters']);
        $container->setParameter('kunstmaan_admin.password_restrictions.min_length', $config['password_restrictions']['min_length']);
        $container->setParameter('kunstmaan_admin.password_restrictions.max_length', $config['password_restrictions']['max_length']);
        $container->setParameter('kunstmaan_admin.enable_toolbar_helper', $config['enable_toolbar_helper']);
        $container->setParameter('kunstmaan_admin.toolbar_firewall_names', !empty($config['provider_keys']) ? $config['provider_keys'] : $config['toolbar_firewall_names']);
        $container->setParameter('kunstmaan_admin.admin_firewall_name', $config['admin_firewall_name']);

        $container->registerForAutoconfiguration(MenuAdaptorInterface::class)
            ->addTag('kunstmaan_admin.menu.adaptor');

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('commands.yml');

        if (!empty($config['enable_console_exception_listener']) && $config['enable_console_exception_listener']) {
            $loader->load('console_listener.yml');
        }

        if (0 !== \count($config['menu_items'])) {
            $this->addSimpleMenuAdaptor($container, $config['menu_items']);
        }

        $this->addWebsiteTitleParameter($container, $config);
        $this->addMultiLanguageParameter($container, $config);
        $this->addRequiredLocalesParameter($container, $config);
        $this->addDefaultLocaleParameter($container, $config);
    }

    public function prepend(ContainerBuilder $container)
    {
        $fosUserOriginalConfig = $container->getExtensionConfig('fos_user');
        if (!isset($fosUserOriginalConfig[0]['db_driver'])) {
            $fosUserConfig['db_driver'] = 'orm'; // other valid values are 'mongodb', 'couchdb'
        }
        $fosUserConfig['from_email']['address'] = 'kunstmaancms@myproject.dev';
        $fosUserConfig['from_email']['sender_name'] = 'KunstmaanCMS';
        $fosUserConfig['firewall_name'] = 'main';
        $fosUserConfig['user_class'] = 'Kunstmaan\AdminBundle\Entity\User';
        $fosUserConfig['group']['group_class'] = 'Kunstmaan\AdminBundle\Entity\Group';
        $fosUserConfig['resetting']['token_ttl'] = 86400;
        // Use this node only if you don't want the global email address for the resetting email
        $fosUserConfig['resetting']['email']['from_email']['address'] = 'kunstmaancms@myproject.dev';
        $fosUserConfig['resetting']['email']['from_email']['sender_name'] = 'KunstmaanCMS';
        $fosUserConfig['resetting']['email']['template'] = '@FOSUser/Resetting/email.txt.twig';
        $fosUserConfig['resetting']['form']['type'] = ResettingFormType::class;
        $fosUserConfig['resetting']['form']['name'] = 'fos_user_resetting_form';
        $fosUserConfig['resetting']['form']['validation_groups'] = ['ResetPassword'];

        $fosUserConfig['service']['mailer'] = 'fos_user.mailer.twig_swift';
        $container->prependExtensionConfig('fos_user', $fosUserConfig);

        // Manually register the KunstmaanAdminBundle folder as a FosUser override for symfony 4.
        if ($container->hasParameter('kernel.project_dir') && file_exists($container->getParameter('kernel.project_dir') . '/templates/bundles/KunstmaanAdminBundle')) {
            $twigConfig['paths'][] = ['value' => '%kernel.project_dir%/templates/bundles/KunstmaanAdminBundle', 'namespace' => 'FOSUser'];
        }
        $twigConfig['paths'][] = ['value' => \dirname(__DIR__) . '/Resources/views', 'namespace' => 'FOSUser'];
        $container->prependExtensionConfig('twig', $twigConfig);

        // NEXT_MAJOR: Remove templating dependency

        $configs = $container->getExtensionConfig($this->getAlias());
        $this->processConfiguration(new Configuration(), $configs);
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        return 'http://bundles.kunstmaan.be/schema/dic/admin';
    }

    /**
     * {@inheritdoc}
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__ . '/../Resources/config/schema';
    }

    private function addSimpleMenuAdaptor(ContainerBuilder $container, array $menuItems)
    {
        $definition = new Definition('Kunstmaan\AdminBundle\Helper\Menu\SimpleMenuAdaptor', [
            new Reference('security.authorization_checker'),
            $menuItems,
        ]);
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

    private function addWebsiteTitleParameter(ContainerBuilder $container, array $config)
    {
        $websiteTitle = $config['website_title'];
        if (null === $config['website_title']) {
            @trigger_error('Not providing a value for the "kunstmaan_admin.website_title" config is deprecated since KunstmaanAdminBundle 5.2, this config value will be required in KunstmaanAdminBundle 6.0.', E_USER_DEPRECATED);

            $websiteTitle = $container->hasParameter('websitetitle') ? $container->getParameter('websitetitle') : '';
        }

        $container->setParameter('kunstmaan_admin.website_title', $websiteTitle);
    }

    private function addMultiLanguageParameter(ContainerBuilder $container, array $config)
    {
        $multilanguage = $config['multi_language'];
        if (null === $multilanguage) {
            @trigger_error('Not providing a value for the "kunstmaan_admin.multi_language" config is deprecated since KunstmaanAdminBundle 5.2, this config value will be required in KunstmaanAdminBundle 6.0.', E_USER_DEPRECATED);

            $multilanguage = $container->hasParameter('multilanguage') ? $container->getParameter('multilanguage') : '';
        }

        $container->setParameter('kunstmaan_admin.multi_language', $multilanguage);
    }

    private function addRequiredLocalesParameter(ContainerBuilder $container, array $config)
    {
        $requiredLocales = $config['required_locales'];
        if (null === $config['required_locales']) {
            @trigger_error('Not providing a value for the "kunstmaan_admin.required_locales" config is deprecated since KunstmaanAdminBundle 5.2, this config value will be required in KunstmaanAdminBundle 6.0.', E_USER_DEPRECATED);

            $requiredLocales = $container->hasParameter('requiredlocales') ? $container->getParameter('requiredlocales') : '';
        }

        $container->setParameter('kunstmaan_admin.required_locales', $requiredLocales);
        $container->setParameter('requiredlocales', $requiredLocales); //Keep old parameter for to keep BC with routing config
    }

    private function addDefaultLocaleParameter(ContainerBuilder $container, array $config)
    {
        $defaultLocale = $config['default_locale'];
        if (null === $config['default_locale']) {
            @trigger_error('Not providing a value for the "kunstmaan_admin.default_locale" config is deprecated since KunstmaanAdminBundle 5.2, this config value will be required in KunstmaanAdminBundle 6.0.', E_USER_DEPRECATED);

            $defaultLocale = $container->hasParameter('defaultlocale') ? $container->getParameter('defaultlocale') : '';
        }

        $container->setParameter('kunstmaan_admin.default_locale', $defaultLocale);
    }
}

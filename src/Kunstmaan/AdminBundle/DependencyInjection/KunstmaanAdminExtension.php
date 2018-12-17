<?php

namespace Kunstmaan\AdminBundle\DependencyInjection;

use FOS\UserBundle\Form\Type\ResettingFormType;
use InvalidArgumentException;
use Symfony\Component\Config\FileLocator;
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
        $container->setParameter('kunstmaan_admin.password_restrictions.min_special_characters', $config['password_restrictions']['min_special_characters']);
        $container->setParameter('kunstmaan_admin.password_restrictions.min_length', $config['password_restrictions']['min_length']);
        $container->setParameter('kunstmaan_admin.password_restrictions.max_length', $config['password_restrictions']['max_length']);
        $container->setParameter('kunstmaan_admin.enable_toolbar_helper', $config['enable_toolbar_helper']);
        $container->setParameter('kunstmaan_admin.toolbar_firewall_names', !empty($config['provider_keys']) ? $config['provider_keys'] : $config['toolbar_firewall_names']);
        $container->setParameter('kunstmaan_admin.admin_firewall_name', $config['admin_firewall_name']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('commands.yml');

        if (!empty($config['enable_console_exception_listener']) && $config['enable_console_exception_listener']) {
            $loader->load('console_listener.yml');
        }

        if (0 !== count($config['menu_items'])) {
            $this->addSimpleMenuAdaptor($container, $config['menu_items']);
        }
    }

    public function prepend(ContainerBuilder $container)
    {
        $knpMenuConfig['twig'] = true; // set to false to disable the Twig extension and the TwigRenderer
        $knpMenuConfig['templating'] = false; // if true, enables the helper for PHP templates
        $knpMenuConfig['default_renderer'] = 'twig'; // The renderer to use, list is also available by default
        $container->prependExtensionConfig('knp_menu', $knpMenuConfig);

        $fosUserConfig['db_driver'] = 'orm'; // other valid values are 'mongodb', 'couchdb'
        $fosUserConfig['from_email']['address'] = 'kunstmaancms@myproject.dev';
        $fosUserConfig['from_email']['sender_name'] = 'KunstmaanCMS';
        $fosUserConfig['firewall_name'] = 'main';
        $fosUserConfig['user_class'] = 'Kunstmaan\AdminBundle\Entity\User';
        $fosUserConfig['group']['group_class'] = 'Kunstmaan\AdminBundle\Entity\Group';
        $fosUserConfig['resetting']['token_ttl'] = 86400;
        // Use this node only if you don't want the global email address for the resetting email
        $fosUserConfig['resetting']['email']['from_email']['address'] = 'kunstmaancms@myproject.dev';
        $fosUserConfig['resetting']['email']['from_email']['sender_name'] = 'KunstmaanCMS';
        $fosUserConfig['resetting']['email']['template'] = 'FOSUserBundle:Resetting:email.txt.twig';
        $fosUserConfig['resetting']['form']['type'] = ResettingFormType::class;
        $fosUserConfig['resetting']['form']['name'] = 'fos_user_resetting_form';
        $fosUserConfig['resetting']['form']['validation_groups'] = ['ResetPassword'];
        $container->prependExtensionConfig('fos_user', $fosUserConfig);

        $monologConfig['handlers']['main']['type'] = 'rotating_file';
        $monologConfig['handlers']['main']['path'] = sprintf('%s/%s', $container->getParameter('kernel.logs_dir'), $container->getParameter('kernel.environment'));
        $monologConfig['handlers']['main']['level'] = 'debug';
        $container->prependExtensionConfig('monolog', $monologConfig);

        $twigConfig['paths'][] = ['value' => dirname(__DIR__).'/Resources/views', 'namespace' => 'FOSUser'];
        $container->prependExtensionConfig('twig', $twigConfig);

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
        return __DIR__.'/../Resources/config/schema';
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
}

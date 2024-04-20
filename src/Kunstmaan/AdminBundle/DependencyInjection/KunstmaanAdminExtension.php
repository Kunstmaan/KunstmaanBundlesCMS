<?php

namespace Kunstmaan\AdminBundle\DependencyInjection;

use Kunstmaan\AdminBundle\Attribute\AsMenuAdaptor;
use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Service\AuthenticationMailer\SymfonyMailerService;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Mailer\Mailer;

class KunstmaanAdminExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('commands.yml');

        $container->setParameter('version_checker.url', 'https://kunstmaancms.be/version-check');
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

        $this->configureAuthentication($config, $container, $loader);

        $container->setParameter('kunstmaan_admin.admin_locales', $config['admin_locales']);
        $container->setParameter('kunstmaan_admin.default_admin_locale', $config['default_admin_locale']);

        $container->setParameter('kunstmaan_admin.session_security.ip_check', $config['session_security']['ip_check']);
        $container->setParameter('kunstmaan_admin.session_security.user_agent_check', $config['session_security']['user_agent_check']);

        $container->setParameter('kunstmaan_admin.admin_prefix', $this->normalizeUrlSlice($config['admin_prefix']));

        $container->setParameter('kunstmaan_admin.admin_exception_excludes', $config['exception_logging']['exclude_patterns']);

        $container->setParameter('kunstmaan_admin.password_restrictions.min_digits', $config['password_restrictions']['min_digits']);
        $container->setParameter('kunstmaan_admin.password_restrictions.min_uppercase', $config['password_restrictions']['min_uppercase']);
        $container->setParameter('kunstmaan_admin.password_restrictions.min_special_characters', $config['password_restrictions']['min_special_characters']);
        $container->setParameter('kunstmaan_admin.password_restrictions.min_length', $config['password_restrictions']['min_length']);
        $container->setParameter('kunstmaan_admin.password_restrictions.max_length', $config['password_restrictions']['max_length']);
        $container->setParameter('kunstmaan_admin.enable_toolbar_helper', $config['enable_toolbar_helper']);
        $container->setParameter('kunstmaan_admin.toolbar_firewall_names', $config['toolbar_firewall_names']);
        $container->setParameter('kunstmaan_admin.admin_firewall_name', $config['admin_firewall_name']);

        $container->setParameter('kunstmaan_admin.user_class', $config['authentication']['user_class']);
        $container->setParameter('kunstmaan_admin.group_class', $config['authentication']['group_class']);

        $container->registerForAutoconfiguration(MenuAdaptorInterface::class)
            ->addTag('kunstmaan_admin.menu.adaptor');

        $container->registerAttributeForAutoconfiguration(AsMenuAdaptor::class, static function (ChildDefinition $definition, AsMenuAdaptor $attribute, \Reflector $reflector) {
            $tagAttributes = get_object_vars($attribute);
            $definition->addTag('kunstmaan_admin.menu.adaptor', $tagAttributes);
        });

        if (!empty($config['enable_console_exception_listener']) && $config['enable_console_exception_listener']) {
            $loader->load('console_listener.yml');
        }

        if (0 !== \count($config['menu_items'])) {
            $this->addSimpleMenuAdaptor($container, $config['menu_items']);
        }

        $this->registerExceptionLoggingConfiguration($config['exception_logging'], $container);

        $container->setParameter('kunstmaan_admin.default_locale', $config['default_locale']);
        $container->setParameter('kunstmaan_admin.website_title', $config['website_title']);
        $container->setParameter('kunstmaan_admin.multi_language', $config['multi_language']);
        $container->setParameter('kunstmaan_admin.required_locales', $config['required_locales']);
        $container->setParameter('requiredlocales', $config['required_locales']); // Keep old parameter for to keep BC with routing config

        $container->setParameter('kunstmaan_admin.hide_sidebar', $config['hide_sidebar']);
    }

    public function getNamespace(): string
    {
        return 'https://kunstmaancms.be/schema/dic/admin';
    }

    public function getXsdValidationBasePath(): string
    {
        return __DIR__ . '/../Resources/config/schema';
    }

    private function addSimpleMenuAdaptor(ContainerBuilder $container, array $menuItems): void
    {
        $definition = new Definition('Kunstmaan\AdminBundle\Helper\Menu\SimpleMenuAdaptor', [
            new Reference('security.authorization_checker'),
            $menuItems,
        ]);
        $definition->addTag('kunstmaan_admin.menu.adaptor');

        $container->setDefinition('kunstmaan_admin.menu.adaptor.simple', $definition);
    }

    protected function normalizeUrlSlice(string $urlSlice): string
    {
        /* Get rid of exotic characters that would break the url */
        $urlSlice = filter_var($urlSlice, FILTER_SANITIZE_URL);

        /* Remove leading and trailing slashes */
        $urlSlice = trim($urlSlice, '/');

        /* Make sure our $urlSlice is literally used in our regex */
        $urlSlice = preg_quote($urlSlice);

        return $urlSlice;
    }

    private function registerExceptionLoggingConfiguration(array $config, ContainerBuilder $container): void
    {
        if ($this->isConfigEnabled($container, $config)) {
            return;
        }

        $container->removeDefinition('kunstmaan_admin.exception.listener');
        $container->removeDefinition('kunstmaan_admin.datacollector.exception');

        $definition = $container->getDefinition('kunstmaan_admin.menu.adaptor.settings');
        $definition->setArgument(2, false);
    }

    private function configureAuthentication(array $config, ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->setParameter('kunstmaan_admin.enable_new_cms_authentication', true);

        $loader->load('authentication.yml');

        $container->setAlias('kunstmaan_admin.authentication.mailer', $config['authentication']['mailer']['service']);

        // Validate mailer config
        if (!class_exists(Mailer::class)) {
            throw new LogicException('No mail integration found to enable the authentication mailer. Try running "composer require symfony/mailer".');
        }

        if ($config['authentication']['mailer']['service'] === SymfonyMailerService::class && !class_exists(Mailer::class)) {
            throw new LogicException('Symfony mailer support for the authentication mailer cannot be enabled as the component is not installed. Try running "composer require symfony/mailer".');
        }

        // Cleanup mailer services
        if (!class_exists(Mailer::class)) {
            $container->removeDefinition(SymfonyMailerService::class);
        }

        $mailerAddress = $container->getDefinition('kunstmaan_admin.mailer.default_sender');
        $mailerAddress->setArgument(0, $config['authentication']['mailer']['from_address']);
        $mailerAddress->setArgument(1, $config['authentication']['mailer']['from_name']);
    }
}

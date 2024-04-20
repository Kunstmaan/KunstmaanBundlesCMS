<?php

namespace Kunstmaan\MultiDomainBundle\DependencyInjection;

use Kunstmaan\MultiDomainBundle\EventSubscriber\LogoutHostOverrideCleanupEventSubscriber;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;

class KunstmaanMultiDomainExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $hostConfigurations = $this->getHostConfigurations($config['hosts']);

        $container->setParameter(
            'kunstmaan_multi_domain.hosts',
            $hostConfigurations
        );

        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yml');

        /*
         * We override the default slug router here. You can use a custom one by
         * setting kunstmaan_multi_domain.router.class to your own implementation.
         */
        $container->setParameter(
            'kunstmaan_node.slugrouter.class',
            $container->getParameter('kunstmaan_multi_domain.router.class')
        );

        /*
         * We override the default domain configuration service here. You can use a custom one by
         * setting registering the class as a service and override the "kunstmaan_admin.domain_configuration" service alias.
         */
        $container->setAlias('kunstmaan_admin.domain_configuration', 'kunstmaan_multi_domain.domain_configuration')->setPublic(true);

        $adminFirewall = $container->getParameter('kunstmaan_admin.admin_firewall_name');
        $container->getDefinition(LogoutHostOverrideCleanupEventSubscriber::class)->addTag('kernel.event_subscriber', ['dispatcher' => 'security.event_dispatcher.' . $adminFirewall]);
    }

    /**
     * Convert config hosts array to a usable format
     */
    private function getHostConfigurations($hosts): array
    {
        $hostConfigurations = [];
        foreach ($hosts as $name => $settings) {
            $host = $settings['host'];
            // Set the key of the host as id.
            $hostConfigurations[$host]['id'] = $name;

            foreach ($settings as $setting => $data) {
                if ($setting === 'locales') {
                    $hostConfigurations[$host]['locales_extra'] = $this->getLocalesExtra($data);
                    $data = $this->getHostLocales($data);
                    $hostConfigurations[$host]['reverse_locales'] = array_flip($data);
                }
                $hostConfigurations[$host][$setting] = $data;
            }
        }

        return $hostConfigurations;
    }

    /**
     * Return uri to actual locale mappings
     */
    private function getHostLocales($localeSettings): array
    {
        $hostLocales = [];
        foreach ($localeSettings as $key => $localeMapping) {
            $hostLocales[$localeMapping['uri_locale']] = $localeMapping['locale'];
        }

        return $hostLocales;
    }

    /**
     * Return the extra data configured for each locale
     */
    private function getLocalesExtra($localeSettings): array
    {
        $localesExtra = [];
        foreach ($localeSettings as $key => $localeMapping) {
            $localesExtra[$localeMapping['uri_locale']] = \array_key_exists('extra', $localeMapping) ? $localeMapping['extra'] : [];
        }

        return $localesExtra;
    }
}

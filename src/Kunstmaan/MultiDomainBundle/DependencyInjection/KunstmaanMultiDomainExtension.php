<?php

namespace Kunstmaan\MultiDomainBundle\DependencyInjection;

use Kunstmaan\AdminBundle\Helper\DomainConfiguration;
use Kunstmaan\MultiDomainBundle\Router\DomainBasedLocaleRouter;
use Kunstmaan\NodeBundle\Router\SlugRouter;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link
 * http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KunstmaanMultiDomainExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
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
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yml');
    }

    /**
     * Convert config hosts array to a usable format
     *
     * @param $hosts
     *
     * @return array
     */
    private function getHostConfigurations($hosts)
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
     *
     * @param $localeSettings
     *
     * @return array
     */
    private function getHostLocales($localeSettings)
    {
        $hostLocales = [];
        foreach ($localeSettings as $key => $localeMapping) {
            $hostLocales[$localeMapping['uri_locale']] = $localeMapping['locale'];
        }

        return $hostLocales;
    }

    /**
     * Return the extra data configured for each locale
     *
     * @param $localeSettings
     *
     * @return array
     */
    private function getLocalesExtra($localeSettings)
    {
        $localesExtra = [];
        foreach ($localeSettings as $key => $localeMapping) {
            $localesExtra[$localeMapping['uri_locale']] = array_key_exists('extra', $localeMapping) ? $localeMapping['extra'] : [];
        }

        return $localesExtra;
    }
}

<?php

namespace Kunstmaan\TranslatorBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\Finder\Finder;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KunstmaanTranslatorExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if ($config['enabled'] === false) {
            return;
        }

        $container->setParameter('kuma_translator.enabled', $config['enabled']);
        $container->setParameter('kuma_translator.default_bundle', $config['default_bundle']);
        $container->setParameter('kuma_translator.bundles', $config['bundles']);
        $container->setParameter('kuma_translator.cache_dir', $config['cache_dir']);
        $container->setParameter('kuma_translator.managed_locales', $config['managed_locales']);
        $container->setParameter('kuma_translator.file_formats', $config['file_formats']);
        $container->setParameter('kuma_translator.storage_engine.type', $config['storage_engine']['type']);
        $container->setParameter('kuma_translator.profiler', $container->getParameter('kernel.debug'));
        $container->setParameter('kuma_translator.debug', is_null($config['debug']) ? $container->getParameter('kernel.debug') : $config['debug']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('repositories.yml');

        $this->setTranslationConfiguration($config, $container);
    }

    public function setTranslationConfiguration($config, $container)
    {
        $container->setAlias('translator', 'kunstmaan_translator.service.translator.translator');
        $container->setAlias('translator.default', 'kunstmaan_translator.service.translator.translator');
        $translator = $container->getDefinition('kunstmaan_translator.service.translator.translator');
        $this->registerTranslatorConfiguration($config, $container);

        // overwrites everything
        $translator->addMethodCall('addDatabaseResources', array());

        $translator->addMethodCall('setFallbackLocales', array(array('en')));

        if($container->hasParameter('defaultlocale')) {
            $translator->addMethodCall('setFallbackLocales', array(array($container->getParameter('defaultlocale'))));
        }

        $collector = $container->getDefinition('kunstmaan.data_collector.translator');
        $collector->addArgument($translator);
    }

    /**
     * Symfony\Bundle\FrameworkBundle\DependencyInjection\FrameworkExtension
     * $this->registerTranslatorConfiguration($config['translator'], $container);
     * Used to load all other translation files
     */
    public function registerTranslatorConfiguration($config, $container)
    {
        $translator = $container->getDefinition('kunstmaan_translator.service.translator.translator');

        $dirs = array();
        if (class_exists('Symfony\Component\Validator\Validator')) {
            $r = new \ReflectionClass('Symfony\Component\Validator\Validator');

            $dirs[] = dirname($r->getFilename()).'/Resources/translations';
        }
        if (class_exists('Symfony\Component\Form\Form')) {
            $r = new \ReflectionClass('Symfony\Component\Form\Form');

            $dirs[] = dirname($r->getFilename()).'/Resources/translations';
        }
        $overridePath = $container->getParameter('kernel.root_dir').'/Resources/%s/translations';
        foreach ($container->getParameter('kernel.bundles') as $bundle => $class) {
            $reflection = new \ReflectionClass($class);
            if (is_dir($dir = dirname($reflection->getFilename()).'/Resources/translations')) {
                $dirs[] = $dir;
            }
            if (is_dir($dir = sprintf($overridePath, $bundle))) {
                $dirs[] = $dir;
            }
        }
        if (is_dir($dir = $container->getParameter('kernel.root_dir').'/Resources/translations')) {
            $dirs[] = $dir;
        }

        // Register translation resources
        if (count($dirs) > 0) {
            foreach ($dirs as $dir) {
                $container->addResource(new DirectoryResource($dir));
            }

            $finder = Finder::create();
            $finder->files();

                $finder->filter(function (\SplFileInfo $file) {
                    return 2 === substr_count($file->getBasename(), '.') && preg_match('/\.\w+$/', $file->getBasename());
                });

            $finder->in($dirs);

            foreach ($finder as $file) {
                // filename is domain.locale.format
                list($domain, $locale, $format) = explode('.', $file->getBasename());
                $translator->addMethodCall('addResource', array($format, (string) $file, $locale, $domain));
            }
        }
    }
}

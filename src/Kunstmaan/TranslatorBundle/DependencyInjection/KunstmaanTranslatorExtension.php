<?php

namespace Kunstmaan\TranslatorBundle\DependencyInjection;

use Kunstmaan\TranslatorBundle\Repository\TranslationRepository;
use Kunstmaan\TranslatorBundle\Service\Command\AbstractCommandHandler;
use Kunstmaan\TranslatorBundle\Service\Command\DiffCommand;
use Kunstmaan\TranslatorBundle\Service\Command\Exporter\CSVFileExporter;
use Kunstmaan\TranslatorBundle\Service\Command\Exporter\ExportCommandHandler;
use Kunstmaan\TranslatorBundle\Service\Command\Exporter\Exporter;
use Kunstmaan\TranslatorBundle\Service\Command\Exporter\YamlFileExporter;
use Kunstmaan\TranslatorBundle\Service\Command\Importer\ImportCommandHandler;
use Kunstmaan\TranslatorBundle\Service\Command\Importer\Importer;
use Kunstmaan\TranslatorBundle\Service\Menu\TranslatorMenuAdaptor;
use Kunstmaan\TranslatorBundle\Service\Migrations\MigrationsService;
use Kunstmaan\TranslatorBundle\Service\TranslationFileExplorer;
use Kunstmaan\TranslatorBundle\Service\TranslationGroupManager;
use Kunstmaan\TranslatorBundle\Service\Translator\CacheValidator;
use Kunstmaan\TranslatorBundle\Service\Translator\ResourceCacher;
use Kunstmaan\TranslatorBundle\Service\Translator\Translator;
use Kunstmaan\TranslatorBundle\Toolbar\DataCollectorTranslator;
use Kunstmaan\TranslatorBundle\Toolbar\TranslatorDataCollector;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

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
        $container->setParameter('kuma_translator.debug', \is_null($config['debug']) ? $container->getParameter('kernel.debug') : $config['debug']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('repositories.yml');

        // === BEGIN ALIASES ====
        $container->addAliases(
            [
                'kunstmaan_translator.menu.adaptor' => new Alias(TranslatorMenuAdaptor::class),
                'kunstmaan_translator.service.abstract_command_handler' => new Alias(AbstractCommandHandler::class),
                'kunstmaan_translator.service.importer.command_handler' => new Alias(ImportCommandHandler::class),
                'kunstmaan_translator.service.exporter.command_handler' => new Alias(ExportCommandHandler::class),
                'kunstmaan_translator.service.exporter.exporter' => new Alias(Exporter::class),
                'kunstmaan_translator.service.exporter.yaml' => new Alias(YamlFileExporter::class),
                'kunstmaan_translator.service.exporter.csv' => new Alias(CSVFileExporter::class),
                'kunstmaan_translator.service.file_explorer' => new Alias(TranslationFileExplorer::class),
                'kunstmaan_translator.service.importer.importer' => new Alias(Importer::class),
                'kunstmaan_translator.service.group_manager' => new Alias(TranslationGroupManager::class),
                'kunstmaan_translator.service.translator.loader' => new Alias(\Kunstmaan\TranslatorBundle\Service\Translator\Loader::class),
                'kunstmaan_translator.service.translator.resource_cacher' => new Alias(ResourceCacher::class),
                'kunstmaan_translator.service.translator.cache_validator' => new Alias(CacheValidator::class),
                'kunstmaan_translator.service.translator.translator' => new Alias(Translator::class),
                'kunstmaan_translator.service.migrations.migrations' => new Alias(MigrationsService::class),
                'kunstmaan_translator.service.command.diff' => new Alias(DiffCommand::class),
                'kunstmaan_translator.datacollector' => new Alias(DataCollectorTranslator::class),
                'kunstmaan_translator.datacollector.translations' => new Alias(TranslatorDataCollector::class),
                'kunstmaan_translator.repository.translation' => new Alias(TranslationRepository::class),
            ]
        );

        $this->addParameteredAliases(
            $container,
            [
                ['kunstmaan_translator.menu.adaptor.class', TranslatorMenuAdaptor::class, true],
                ['kunstmaan_translator.service.exporter.csv.class', CSVFileExporter::class, true],
                ['kunstmaan_translator.toolbar.collector.translator.class', TranslatorDataCollector::class, true],
            ]
        );
        // === END ALIASES ====

        $this->setTranslationConfiguration($config, $container);
        $container->getDefinition(DataCollectorTranslator::class)->setDecoratedService('translator');
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

    public function setTranslationConfiguration($config, ContainerBuilder $container)
    {
        $container->setAlias('translator', Translator::class);
        $container->setAlias('translator.default', Translator::class);
        $translator = $container->getDefinition(Translator::class);
        $this->registerTranslatorConfiguration($config, $container);

        // overwrites everything
        $translator->addMethodCall('addDatabaseResources', []);

        $translator->addMethodCall('setFallbackLocales', [['en']]);

        if ($container->hasParameter('defaultlocale')) {
            $translator->addMethodCall('setFallbackLocales', [[$container->getParameter('defaultlocale')]]);
        }
    }

    /**
     * Symfony\Bundle\FrameworkBundle\DependencyInjection\FrameworkExtension
     * $this->registerTranslatorConfiguration($config['translator'], $container);
     * Used to load all other translation files
     */
    public function registerTranslatorConfiguration($config, ContainerBuilder $container)
    {
        $translator = $container->getDefinition(Translator::class);

        $dirs = [];
        if (class_exists('Symfony\Component\Validator\Validation')) {
            $r = new \ReflectionClass('Symfony\Component\Validator\Validation');

            $dirs[] = \dirname($r->getFileName()).'/Resources/translations';
        }
        if (class_exists('Symfony\Component\Form\Form')) {
            $r = new \ReflectionClass('Symfony\Component\Form\Form');

            $dirs[] = \dirname($r->getFileName()).'/Resources/translations';
        }
        $overridePath = $container->getParameter('kernel.root_dir').'/Resources/%s/translations';
        foreach ($container->getParameter('kernel.bundles') as $bundle => $class) {
            $reflection = new \ReflectionClass($class);
            if (is_dir($dir = \dirname($reflection->getFileName()).'/Resources/translations')) {
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
        if (\count($dirs) > 0) {
            foreach ($dirs as $dir) {
                $container->addResource(new DirectoryResource($dir));
            }

            $finder = Finder::create();
            $finder->files();

            $finder->filter(
                function (\SplFileInfo $file) {
                    return 2 === substr_count($file->getBasename(), '.') && preg_match('/\.\w+$/', $file->getBasename());
                }
            );

            $finder->in($dirs);

            foreach ($finder as $file) {
                // filename is domain.locale.format
                list($domain, $locale, $format) = explode('.', $file->getBasename());
                $translator->addMethodCall('addResource', [$format, (string) $file, $locale, $domain]);
            }
        }
    }
}

<?php

namespace Kunstmaan\TranslatorBundle\DependencyInjection\Compiler;

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
use Kunstmaan\TranslatorBundle\Service\Translator\Loader;
use Kunstmaan\TranslatorBundle\Service\Translator\ResourceCacher;
use Kunstmaan\TranslatorBundle\Service\Translator\Translator;
use Kunstmaan\TranslatorBundle\Toolbar\DataCollectorTranslator;
use Kunstmaan\TranslatorBundle\Toolbar\TranslatorDataCollector;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class DeprecationsCompilerPass
 *
 * @package Kunstmaan\TranslatorBundle\DependencyInjection\Compiler
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
                ['kunstmaan_translator.menu.adaptor', TranslatorMenuAdaptor::class],
                ['kunstmaan_translator.service.abstract_command_handler', AbstractCommandHandler::class],
                ['kunstmaan_translator.service.importer.command_handler', ImportCommandHandler::class],
                ['kunstmaan_translator.service.exporter.command_handler', ExportCommandHandler::class],
                ['kunstmaan_translator.service.exporter.exporter', Exporter::class],
                ['kunstmaan_translator.service.exporter.yaml', YamlFileExporter::class],
                ['kunstmaan_translator.service.exporter.csv', CSVFileExporter::class],
                ['kunstmaan_translator.service.file_explorer', TranslationFileExplorer::class],
                ['kunstmaan_translator.service.importer.importer', Importer::class],
                ['kunstmaan_translator.service.group_manager', TranslationGroupManager::class],
                ['kunstmaan_translator.service.translator.loader', Loader::class],
                ['kunstmaan_translator.service.translator.resource_cacher', ResourceCacher::class],
                ['kunstmaan_translator.service.translator.cache_validator', CacheValidator::class],
                ['kunstmaan_translator.service.translator.translator', Translator::class],
                ['kunstmaan_translator.service.migrations.migrations', MigrationsService::class],
                ['kunstmaan_translator.service.command.diff', DiffCommand::class],
                ['kunstmaan_translator.datacollector', DataCollectorTranslator::class],
                ['kunstmaan_translator.datacollector.translations', TranslatorDataCollector::class],
                ['kunstmaan_translator.repository.translation', TranslationRepository::class],
            ]
        );

        $this->addDeprecatedChildDefinitions(
            $container,
            [
                ['kunstmaan_translator.menu.adaptor.class', TranslatorMenuAdaptor::class],
                ['kunstmaan_translator.service.exporter.csv.class', CSVFileExporter::class],
                ['kunstmaan_translator.toolbar.collector.translator.class', TranslatorDataCollector::class],
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
                    'Override service class with "%service_id%" is deprecated since KunstmaanTranslatorBundle 5.1 and will be removed in 6.0. Override the service instance instead.'
                );
                $container->setDefinition($class, $definition);
            } else {
                $definition->setClass($deprecation[1]);
                $definition->setDeprecated(
                    true,
                    'Passing a "%service_id%" instance is deprecated since KunstmaanTranslatorBundle 5.1 and will be removed in 6.0. Use the FQCN instead.'
                );
                $container->setDefinition($deprecation[0], $definition);
            }
        }
    }
}

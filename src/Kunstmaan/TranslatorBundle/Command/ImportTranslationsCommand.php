<?php

namespace Kunstmaan\TranslatorBundle\Command;

use Kunstmaan\TranslatorBundle\Model\Import\ImportCommand;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportTranslationsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
        ->setName('kuma:translator:import')
        ->setDescription('Import translation files into database')
        ->addOption('force',        'f',    InputOption::VALUE_NONE,        'Force import, overwrite all existing database entries')
        ->addOption('locales',      'l',    InputOption::VALUE_REQUIRED,    'Language import, only import a specific locale')
        ->addOption('globals',      'g',    InputOption::VALUE_NONE,        'Global app import, import the global translations of your app')
        ->addOption('bundle',       'b',    InputOption::VALUE_REQUIRED,    'Bundle import, import the translations of a specific bundle, use "all" for all bundles')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $force          = $input->getOption('force');
        $locales        = $input->getOption('locales');
        $globals        = $input->getOption('globals');
        $bundle         = $input->getOption('bundle') ;

        $importCommand = new ImportCommand();
        $importCommand
            ->setForce($force)
            ->setLocales($locales)
            ->setGlobals($globals)
            ->setBundle($bundle);

        $imported = $this->getContainer()->get('kunstmaan_translator.service.importer.command_handler')->executeImportCommand($importCommand);
        
        $output->writeln(sprintf("Translation imported: %d", $imported));

    }
}

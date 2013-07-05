<?php

namespace Kunstmaan\TranslatorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Kunstmaan\TranslatorBundle\Model\Import\ImportCommand;

class ImportTranslationsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
        ->setName('kuma:translator:import')
        ->setDescription('Import translation files into database')
        ->addOption('force',        'f',    InputOption::VALUE_NONE, 'Force import, overwrite all existing database entries')
        ->addOption('locale',       'l',    InputOption::VALUE_REQUIRED, 'Language import, only import a specific locale')
        ->addOption('globals',      'g',    InputOption::VALUE_NONE, 'Global app import, import the global translations of your app')
        ->addOption('bundle',       'b',    InputOption::VALUE_REQUIRED, 'Bundle import, import the translations of a specific bundle, use "all" for all bundles')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $force =        $input->getOption('force');
        $locale =       $input->getOption('locale');
        $globals =      $input->getOption('globals');
        $bundle =       $input->getOption('bundle') ;

        $importCommand = new ImportCommand();
        $importCommand
            ->setForce($force)
            ->setLocale($locale)
            ->setGlobals($globals)
            ->setBundle($bundle);

        $this->getContainer()->get('kunstmaan_translator.service.importer.command_handler')->executeImportCommand($importCommand);

    }
}

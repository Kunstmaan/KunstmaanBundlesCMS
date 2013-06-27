<?php

namespace Kunstmaan\TranslatorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

use Kunstmaan\TranslatorBundle\Model\Import\ImportCommand;

class ImportTranslationsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
        ->setName('kuma_translator:import')
        ->setDescription('Import translation files into database')
        ->addOption('force',        'f',    InputOption::VALUE_NONE, 'Force import, overwrite all existing database entries')
        ->addOption('locale',       'l',    InputOption::VALUE_NONE, 'Language import, only import a specific locale')
        ->addOption('globals',      'g',    InputOption::VALUE_NONE, 'Global app import, import the global translations of your app')
        ->addOption('bundle',       'b',    InputOption::VALUE_NONE, 'Bundle import, import the translations of a specific bundle')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $force =        $input->getOption('force');
        $locale =       $input->getOption('locale');
        $globals =      $input->getArgument('globals');
        $bundle =       $input->getArgument('bundle');

        $importCommand = new ImportCommand();
        $importCommand
            ->setForce($force)
            ->setLanguage($locale)
            ->setGlobals($globals)
            ->setBundle($bundle);

        $this->getContainer()->get('kunstmaan_translator.service.importer.command_handler')->executeImportCommand($importCommand);

    }
}

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
            ->addOption('force',         'f', InputOption::VALUE_NONE,     'Force import, overwrite all existing database entries')
            ->addOption('locales',       'l', InputOption::VALUE_REQUIRED, 'Language import, only import a specific locale')
            ->addOption('globals',       'g', InputOption::VALUE_NONE,     'Global app import, import the global translations of your app')
            ->addOption('defaultbundle', 'd', InputOption::VALUE_REQUIRED, 'Import the translations for specific bundles, use "own", "all" or "custom"')
            ->addOption('bundles',       'b', InputOption::VALUE_OPTIONAL, 'A list of bundle names that need to be imported (comma delimited) , only used When "defaultbundle" is set to "custom"')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $force          = $input->getOption('force');
        $locales        = $input->getOption('locales');
        $globals        = $input->getOption('globals');
        $defaultBundle  = $input->getOption('defaultbundle') ;
        $bundles        = $input->hasOption('bundles') ? array_map('trim', explode(',', $input->getOption('bundles'))) : array();

        $importCommand = new ImportCommand();
        $importCommand
            ->setForce($force)
            ->setLocales($locales)
            ->setGlobals($globals)
            ->setDefaultBundle($defaultBundle)
            ->setBundles($bundles);

        $imported = $this->getContainer()->get('kunstmaan_translator.service.importer.command_handler')->executeImportCommand($importCommand);
        
        $output->writeln(sprintf("Translation imported: %d", $imported));

    }
}

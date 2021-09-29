<?php

namespace Kunstmaan\TranslatorBundle\Command;

use Kunstmaan\TranslatorBundle\Model\Import\ImportCommand;
use Kunstmaan\TranslatorBundle\Service\Command\Importer\ImportCommandHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * NEXT_MAJOR file will be renamed
 */
final class ImportTranslationsCommand extends Command
{
    /**
     * @var ImportCommandHandler
     */
    private $importCommandHandler;

    /**
     * @var string
     */
    private $defaultBundle;

    /**
     * @var array
     */
    private $bundles;

    public function __construct(ImportCommandHandler $importCommandHandler, string $defaultBundle, array $bundles)
    {
        parent::__construct();

        $this->importCommandHandler = $importCommandHandler;
        $this->defaultBundle = $defaultBundle;
        $this->bundles = $bundles;
    }

    /**
     * Configures this command.
     */
    protected function configure()
    {
        $this
            ->setName('kuma:translator:import')
            ->setDescription('Import translation files into database')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force import, overwrite all existing database entries')
            ->addOption('locales', 'l', InputOption::VALUE_REQUIRED, 'Language import, only import a specific locale')
            ->addOption('globals', 'g', InputOption::VALUE_NONE, 'Global app import, import the global translations of your app')
            ->addOption(
                'defaultbundle',
                'd',
                InputOption::VALUE_REQUIRED,
                'Import the translations for specific bundles, use "own", "all" or "custom"'
            )
            ->addOption(
                'bundles',
                'b',
                InputOption::VALUE_OPTIONAL,
                'A list of bundle names that need to be imported (comma delimited) , only used When "defaultbundle" is set to "custom"'
            );
    }

    /**
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $force = $input->getOption('force');
        $locales = $input->getOption('locales');
        $globals = $input->getOption('globals');
        $defaultBundle = $input->getOption('defaultbundle') ?: $this->defaultBundle;
        $bundles = $input->getOption('bundles') ? array_map('trim', explode(',', $input->getOption('bundles'))) : $this->bundles;

        $importCommand = new ImportCommand();
        $importCommand
            ->setForce($force)
            ->setLocales($locales)
            ->setGlobals($globals)
            ->setDefaultBundle($defaultBundle)
            ->setBundles($bundles);

        $imported = $this->importCommandHandler->executeImportCommand($importCommand);

        $output->writeln(sprintf('Translation imported: %d', $imported));

        return 0;
    }
}

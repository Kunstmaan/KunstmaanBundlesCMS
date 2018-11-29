<?php

namespace Kunstmaan\TranslatorBundle\Command;

use Kunstmaan\TranslatorBundle\Model\Import\ImportCommand;
use Kunstmaan\TranslatorBundle\Service\Command\Importer\ImportCommandHandler;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @final since 5.1
 *
 * @deprecated since 5.1
 * NEXT_MAJOR extend from `Command` and remove `$this->getContainer` usages
 * NEXT_MAJOR file will be renamed
 *
 * Class ImportTranslationsFromCodeCommand
 */
class ImportTranslationsCommand extends ContainerAwareCommand
{
    /**
     * @var ImportCommandHandler
     */
    private $importCommandHandler;

    /**
     * @param ImportCommandHandler|null $importCommandHandler
     */
    public function __construct(/* ImportCommandHandler */ $importCommandHandler = null)
    {
        parent::__construct();

        if (!$importCommandHandler instanceof ImportCommandHandler) {
            @trigger_error(sprintf('Passing a command name as the first argument of "%s" is deprecated since version symfony 3.4 and will be removed in symfony 4.0. If the command was registered by convention, make it a service instead. ', __METHOD__), E_USER_DEPRECATED);

            $this->setName(null === $importCommandHandler ? 'kuma:translator:import' : $importCommandHandler);

            return;
        }

        $this->importCommandHandler = $importCommandHandler;
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
            ->addOption('defaultbundle', 'd', InputOption::VALUE_REQUIRED, 'Import the translations for specific bundles, use "own", "all" or "custom"')
            ->addOption('bundles', 'b', InputOption::VALUE_OPTIONAL, 'A list of bundle names that need to be imported (comma delimited) , only used When "defaultbundle" is set to "custom"')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $force = $input->getOption('force');
        $locales = $input->getOption('locales');
        $globals = $input->getOption('globals');
        $defaultBundle = $input->getOption('defaultbundle');
        $bundles = $input->hasOption('bundles') ? array_map('trim', explode(',', $input->getOption('bundles'))) : array();
        if (null === $this->importCommandHandler) {
            $this->importCommandHandler = $this->getContainer()->get('kunstmaan_translator.service.importer.command_handler');
        }

        $importCommand = new ImportCommand();
        $importCommand
            ->setForce($force)
            ->setLocales($locales)
            ->setGlobals($globals)
            ->setDefaultBundle($defaultBundle)
            ->setBundles($bundles);

        $imported = $this->importCommandHandler->executeImportCommand($importCommand);

        $output->writeln(sprintf('Translation imported: %d', $imported));
    }
}

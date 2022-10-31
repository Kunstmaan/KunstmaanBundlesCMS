<?php

namespace Kunstmaan\TranslatorBundle\Command;

use Kunstmaan\TranslatorBundle\Service\Command\Importer\Importer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsCommand(name: 'kuma:translator:import-file', description: 'Import file with translations, supported formats are xlsx, ods, csv')]
final class ImportTranslationsFromFileCommand extends Command
{
    /** @var Importer */
    private $importer;

    /** @var TranslatorInterface */
    private $translator;

    /** @var array */
    private $locales;

    /**
     * @param array $locales
     */
    public function __construct(Importer $importer, TranslatorInterface $translator, $locales)
    {
        parent::__construct();
        $this->importer = $importer;
        $this->translator = $translator;
        $this->locales = $locales;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'The full path of the file')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force import, overwrite all existing database entries')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');
        $force = $input->getOption('force');

        $this->importer->importFromSpreadsheet($file, $this->locales, $force);
        if ($force) {
            $confirmation = $this->translator->trans('kuma_translator.command.import.flash.force_success');
        } else {
            $confirmation = $this->translator->trans('kuma_translator.command.import.flash.success');
        }
        $output->writeln($confirmation);

        return 0;
    }
}

<?php

namespace Kunstmaan\TranslatorBundle\Command;

use Kunstmaan\TranslatorBundle\Service\Command\Importer\Importer;
use Kunstmaan\TranslatorBundle\Service\Translator\Translator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ImportTranslationsFromFileCommand
 */
final class ImportTranslationsFromFileCommand extends Command
{
    /** @var Importer */
    private $importer;

    /** @var TranslatorInterface */
    private $translator;

    /** @var array */
    private $locales;

    /**
     * ImportTranslationsFromFileCommand constructor.
     *
     * @param Importer   $importer
     * @param Translator $translator
     * @param            $locales
     */
    public function __construct(Importer $importer, TranslatorInterface $translator, $locales)
    {
        parent::__construct();
        $this->importer = $importer;
        $this->translator = $translator;
        $this->locales = explode('|', $locales);
    }

    /**
     * Configures this command
     */
    protected function configure()
    {
        $this
            ->setName('kuma:translator:import-file')
            ->setDescription('Import file with translations, supported formats are xlsx, ods, csv')
            ->addArgument('file', InputArgument::REQUIRED, 'The full path of the file')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force import, overwrite all existing database entries');
    }

    /**
     * @throws LogicException
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
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
    }
}

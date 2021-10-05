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
use Symfony\Component\Translation\TranslatorInterface as LegacyTranslatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ImportTranslationsFromFileCommand extends Command
{
    /** @var Importer */
    private $importer;

    /** @var TranslatorInterface|LegacyTranslatorInterface */
    private $translator;

    /** @var array */
    private $locales;

    /**
     * @param Translator|LegacyTranslatorInterface $translator
     * @param array                                $locales
     */
    public function __construct(Importer $importer, /*TranslatorInterface*/ $translator, $locales)
    {
        if (null !== $translator && (!$translator instanceof LegacyTranslatorInterface && !$translator instanceof TranslatorInterface)) {
            throw new \InvalidArgumentException(sprintf('Argument 2 passed to "%s" must be of the type "%s" or "%s", "%s" given', __METHOD__, LegacyTranslatorInterface::class, TranslatorInterface::class, get_class($translator)));
        }

        parent::__construct();
        $this->importer = $importer;
        $this->translator = $translator;
        $this->locales = $locales;
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
     * @return int
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

        return 0;
    }
}

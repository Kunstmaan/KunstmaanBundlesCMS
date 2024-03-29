<?php

namespace Kunstmaan\TranslatorBundle\Command;

use Kunstmaan\TranslatorBundle\Repository\TranslationRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to reset/request translation flags from the stash
 */
#[AsCommand(name: 'kuma:translator:flag', description: 'Reset translation flags')]
final class TranslationFlagCommand extends Command
{
    /**
     * @var TranslationRepository
     */
    private $translationRepository;

    public function __construct(TranslationRepository $translationRepository)
    {
        parent::__construct();

        $this->translationRepository = $translationRepository;
    }

    protected function configure(): void
    {
        $this
            ->addOption('reset', 'r', InputOption::VALUE_NONE, 'Reset all flags to null in stash')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getOption('reset')) {
            $this->resetAllTranslationFlags();
            $output->writeln('<info>All translation and translation domain flags are reset.</info>');
        }

        return 0;
    }

    /**
     * Rest all flags of all translations and all domains
     */
    public function resetAllTranslationFlags()
    {
        $this->translationRepository->resetAllFlags();
    }
}

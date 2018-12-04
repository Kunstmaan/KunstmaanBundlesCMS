<?php

namespace Kunstmaan\TranslatorBundle\Command;

use Kunstmaan\TranslatorBundle\Repository\TranslationRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to reset/request translation flags from the stash
 *
 * @final since 5.1
 * NEXT_MAJOR extend from `Command` and remove `$this->getContainer` usages
 */
class TranslationFlagCommand extends ContainerAwareCommand
{
    /**
     * @var TranslationRepository
     */
    private $translationRepository;

    /**
     * @param TranslationRepository|null $translationRepository
     */
    public function __construct(/* TranslationRepository */ $translationRepository = null)
    {
        parent::__construct();

        if (!$translationRepository instanceof TranslationRepository) {
            @trigger_error(sprintf('Passing a command name as the first argument of "%s" is deprecated since version symfony 3.4 and will be removed in symfony 4.0. If the command was registered by convention, make it a service instead. ', __METHOD__), E_USER_DEPRECATED);

            $this->setName(null === $translationRepository ? 'kuma:translator:flag' : $translationRepository);

            return;
        }

        $this->translationRepository = $translationRepository;
    }

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('kuma:translator:flag')
            ->setDescription('Reset translation flags')
            ->addOption('reset', 'r', InputOption::VALUE_NONE, 'Reset all flags to null in stash')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('reset')) {
            $this->resetAllTranslationFlags();
            $output->writeln('<info>All translation and translation domain flags are reset.</info>');
        }
    }

    /**
     * Rest all flags of all translations and all domains
     */
    public function resetAllTranslationFlags()
    {
        if (null === $this->translationRepository) {
            $this->translationRepository = $this->getContainer()->get('kunstmaan_translator.repository.translation');
        }

        $this->translationRepository->resetAllFlags();
    }
}

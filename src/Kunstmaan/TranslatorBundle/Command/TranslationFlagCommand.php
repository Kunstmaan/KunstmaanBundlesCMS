<?php
namespace Kunstmaan\TranslatorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to reset/request translation flags from the stash
 */
class TranslationFlagCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('kuma:translator:flag')
            ->setDescription('Reset translation flags')
            ->addOption('reset',    'r',    InputOption::VALUE_NONE,        'Reset all flags to null in stash')
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
        $this->getContainer()->get('kunstmaan_translator.service.manager')->resetAllTranslationFlags();
    }
}

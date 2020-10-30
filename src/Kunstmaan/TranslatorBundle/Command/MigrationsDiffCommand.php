<?php

namespace Kunstmaan\TranslatorBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Command\Proxy\DoctrineCommandHelper;
use Doctrine\Bundle\MigrationsBundle\Command\DoctrineCommand;
use Kunstmaan\TranslatorBundle\Service\Command\DiffCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

if (class_exists(\Doctrine\Migrations\Tools\Console\Command\GenerateCommand::class)) {
    /**
     * Command for generate migration classes by checking the translation flag value
     *
     * @final since 5.1
     *
     * @deprecated This class is deprecated since KunstmaanTranslatorBundle 5.2 and will be removed in 6.0.
     */
    class MigrationsDiffCommand extends DiffCommand
    {
        protected function configure(): void
        {
            parent::configure();

            $this
                ->setName('kuma:translator:migrations:diff')
                ->addOption('em', null, InputOption::VALUE_OPTIONAL, 'The entity manager to use for this command.')
            ;
        }

        public function execute(InputInterface $input, OutputInterface $output): ?int
        {
            DoctrineCommandHelper::setApplicationEntityManager($this->getApplication(), $input->getOption('em'));

            $configuration = $this->getMigrationConfiguration($input, $output);
            DoctrineCommand::configureMigrations($this->getApplication()->getKernel()->getContainer(), $configuration);

            $exitCode = parent::execute($input, $output);

            return is_numeric($exitCode) ? (int) $exitCode : 0;
        }
    }
} else {
    /**
     * Command for generate migration classes by checking the translation flag value
     *
     * @final since 5.1
     *
     * @deprecated This class is deprecated since KunstmaanTranslatorBundle 5.2 and will be removed in 6.0.
     */
    class MigrationsDiffCommand extends DiffCommand
    {
        protected function configure()
        {
            parent::configure();

            $this
                ->setName('kuma:translator:migrations:diff')
                ->addOption('em', null, InputOption::VALUE_OPTIONAL, 'The entity manager to use for this command.')
            ;
        }

        public function execute(InputInterface $input, OutputInterface $output)
        {
            DoctrineCommandHelper::setApplicationEntityManager($this->getApplication(), $input->getOption('em'));

            $configuration = $this->getMigrationConfiguration($input, $output);
            DoctrineCommand::configureMigrations($this->getApplication()->getKernel()->getContainer(), $configuration);

            $exitCode = parent::execute($input, $output);

            return is_numeric($exitCode) ? (int) $exitCode : 0;
        }
    }
}

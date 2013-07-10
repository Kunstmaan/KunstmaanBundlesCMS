<?php
namespace Kunstmaan\TranslatorBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\DoctrineCommandHelper;
use Kunstmaan\TranslatorBundle\Service\Command\DiffCommand;
use Doctrine\Bundle\MigrationsBundle\Command\DoctrineCommand;

/**
 * Command for generate migration classes by checking the translation flag value
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

        parent::execute($input, $output);
    }
}

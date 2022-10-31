<?php

namespace Kunstmaan\DashboardBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\DashboardBundle\Entity\AnalyticsConfig;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'kuma:dashboard:widget:googleanalytics:data:flush', description: 'Flush the data of a config')]
final class GoogleAnalyticsDataFlushCommand extends Command
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();

        $this->em = $em;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('config', InputArgument::OPTIONAL, 'The config id')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configRepository = $this->em->getRepository(AnalyticsConfig::class);

        $configId = $input->getArgument('config') ? $input->getArgument('config') : false;

        try {
            $configRepository->flushConfig($configId);
            $output->writeln('<fg=green>Data flushed</fg=green>');

            return 0;
        } catch (\Exception $e) {
            $output->writeln('<fg=red>' . $e->getMessage() . '</fg=red>');

            return 1;
        }
    }
}

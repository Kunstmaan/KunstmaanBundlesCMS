<?php

namespace Kunstmaan\DashboardBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class GoogleAnalyticsDataFlushCommand extends Command
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();

        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setName('kuma:dashboard:widget:googleanalytics:data:flush')
            ->setDescription('Flush the data of a config')
            ->addArgument(
                'config',
                InputArgument::OPTIONAL,
                'The config id'
            );
    }

    /**
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig');

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

<?php
namespace Kunstmaan\DashboardBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class GoogleAnalyticsDataFlushCommand extends ContainerAwareCommand
{

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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $configRepository = $em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig');

        $configId = $input->getArgument('config') ? $input->getArgument('config') : false;
        try {
            $configRepository->flushConfig($configId);
            $output->writeln('<fg=green>Data flushed</fg=green>');
        } catch (\Exception $e) {
            $output->writeln('<fg=red>'.$e->getMessage().'</fg=red>');
        }
    }
}

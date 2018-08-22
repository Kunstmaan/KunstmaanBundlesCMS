<?php
namespace Kunstmaan\DashboardBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GoogleAnalyticsConfigFlushCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('kuma:dashboard:widget:googleanalytics:config:flush')
            ->setDescription('Flush configs')
            ->addOption(
                'config',
                null,
                InputOption::VALUE_OPTIONAL,
                'Specify to only flush one config',
                false
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $configRepository = $em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig');
        $configId  = $input->getOption('config');
        $configs = array();

        try {
            if ($configId) {
                $configs[] = $configRepository->find($configId);
            } else {
                $configs = $configRepository->findAll();
            }

            foreach ($configs as $config) {
                $em->remove($config);
            }
            $em->flush();
            $output->writeln('<fg=green>Config flushed</fg=green>');
        } catch (\Exception $e) {
            $output->writeln('<fg=red>'.$e->getMessage().'</fg=red>');
        }
    }
}

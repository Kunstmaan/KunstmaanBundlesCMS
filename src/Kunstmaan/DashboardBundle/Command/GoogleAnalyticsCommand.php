<?php
namespace Kunstmaan\DashboardBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GoogleAnalyticsCommand extends ContainerAwareCommand {

    protected function configure()
    {
        $this
            ->setName('kuma:dashboard:widget:googleanalytics')
            ->setDescription('Collect the Google Analytics dashboard widget data');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
       //skip
    }


}
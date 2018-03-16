<?php

namespace Kunstmaan\DashboardBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GoogleAnalyticsConfigsListCommand
 */
class GoogleAnalyticsConfigsListCommand extends ContainerAwareCommand
{
    /** @var EntityManagerInterface $em */
    private $em;

    protected function configure()
    {
        $this
            ->setName('kuma:dashboard:widget:googleanalytics:configs:list')
            ->setDescription('List available configs');
    }

    /**
     * Inits instance variables for global usage.
     */
    private function init()
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->init();

        $configs = $this->getConfigs();

        if (\count($configs)) {
            $result = "\t".'<fg=green>'.\count($configs).'</fg=green> configs found:';
            $output->writeln($result);
            foreach ($configs as $config) {
                $result = "\t".'(id: <fg=cyan>'.$config->getId().'</fg=cyan>)';
                $result .= "\t".$config->getName();

                $output->writeln($result);
            }
        } else {
            $output->writeln('No configs found');
        }

    }

    /**
     * get all segments
     *
     * @return array
     */
    private function getConfigs()
    {
        // get all segments
        $configRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig');

        return $configRepository->findAll();
    }
}

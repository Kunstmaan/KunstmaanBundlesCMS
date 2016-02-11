<?php
namespace Kunstmaan\DashboardBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class GoogleAnalyticsOverviewsListCommand extends ContainerAwareCommand
{
    /** @var EntityManager $em */
    private $em;

    protected function configure()
    {
        $this
            ->setName('kuma:dashboard:widget:googleanalytics:overviews:list')
            ->setDescription('List available overviews')
            ->addOption(
                'config',
                null,
                InputOption::VALUE_OPTIONAL,
                'Specify to only list overviews of one config',
                false
            )
            ->addOption(
                'segment',
                null,
                InputOption::VALUE_OPTIONAL,
                'Specify to only list overviews of one segment',
                false
            );
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

        // get params
        $configId  = $input->getOption('config');
        $segmentId = $input->getOption('segment');

        try {
            $overviews = array();

            if ($segmentId) {
                $overviews = $this->getOverviewsOfSegment($segmentId);
            } else if ($configId) {
                $overviews = $this->getOverviewsOfConfig($configId);
            } else {
                $overviews = $this->getAllOverviews();
            }

            if (count($overviews)) {
                $result = "\t".'<fg=green>' . count($overviews) . '</fg=green> overviews found:';
                $output->writeln($result);
                foreach($overviews as $overview) {
                    $result = "\t".'(id: <fg=cyan>' .$overview->getId() . '</fg=cyan>)';
                    $result .= "\t".'(config: <fg=cyan>' .$overview->getconfig()->getId() . '</fg=cyan>)';
                    if ($overview->getSegment()) {
                        $result .= "\t".'(segment: <fg=cyan>' .$overview->getSegment()->getId() . '</fg=cyan>)';
                    } else {
                        $result .= "\t\t";
                    }
                    $result .= "\t" . $overview->getTitle();

                    $output->writeln($result);
                }
            } else {
                $output->writeln('No overviews found');
            }
        } catch (\Exception $e) {
            $output->writeln('<fg=red>'.$e->getMessage().'</fg=red>');
        }

    }


    /**
     * get all overviews of a segment
     * @param int $segmentId
     * @return array
     */
    private function getOverviewsOfSegment($segmentId)
    {
        // get specified segment
        $segmentRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsSegment');
        $segment = $segmentRepository->find($segmentId);

        if (!$segment) {
            throw new \Exception('Unkown segment ID');
        }

        // get the overviews
        return $segment->getOverviews();
    }

    /**
     * get all overviews of a config
     * @param int $configId
     * @return array
     */
    private function getOverviewsOfConfig($configId)
    {
        // get specified config
        $configRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig');
        $config = $configRepository->find($configId);

        if (!$config) {
            throw new \Exception('Unkown config ID');
        }

        // get the overviews
        return $config->getOverviews();
    }

    /**
     * get all overviews
     *
     * @return array
     */
    private function getAllOverviews()
    {
        // get all overviews
        $overviewRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsOverview');
        return $overviewRepository->findAll();
    }
}

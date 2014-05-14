<?php
namespace Kunstmaan\DashboardBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class GoogleAnalyticsOverviewsGenerateCommand extends ContainerAwareCommand
{
    /** @var EntityManager $em */
    private $em;

    protected function configure()
    {
        $this
            ->setName('ga:overviews:generate')
            ->setDescription('Generate overviews')
            ->addOption(
                'config',
                null,
                InputOption::VALUE_OPTIONAL,
                'Specify to only update one config',
                false
            )
            ->addOption(
                'segment',
                null,
                InputOption::VALUE_OPTIONAL,
                'Specify to only update one segment',
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
        $configId = false;
        $segmentId = false;
        try {
            $configId  = $input->getOption('config');
            $segmentId = $input->getOption('segment');
        } catch (\Exception $e) {}

        try {
            $overviews = array();

            if ($segmentId) {
               $this->generateOverviewsOfSegment($segmentId);
            } else if ($configId) {
                $this->generateOverviewsOfConfig($configId);
            } else {
                $this->generateAllOverviews();
            }

            $output->writeln('<fg=green>Overviews succesfully generated</fg=green>');
        } catch (\Exception $e) {
            $output->writeln('<fg=red>'.$e->getMessage().'</fg=red>');
        }

    }


    /**
     * get all overviews of a segment
     * @param int $segmentId
     * @return array
     */
    private function generateOverviewsOfSegment($segmentId)
    {
        // get specified segment
        $segmentRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsSegment');
        $segment = $segmentRepository->find($segmentId);

        if (!$segment) {
            throw new \Exception('Unkown segment ID');
        }

        // init the segment
        $segmentRepository->initSegment($segment);
    }

    /**
     * get all overviews of a config
     * @param int $configId
     * @return array
     */
    private function generateOverviewsOfConfig($configId)
    {
        $configRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig');
        $segmentRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsSegment');
        $overviewRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsOverview');
        // get specified config
        $config = $configRepository->find($configId);

        if (!$config) {
            throw new \Exception('Unkown config ID');
        }

        // create default overviews for this config if none exist yet
        if (!count($config->getOverviews())) {
            $overviewRepository->addOverviews($config);
        }

        // init all the segments for this config
        $segments = $config->getSegments();
        foreach ($segments as $segment) {
            $segmentRepository->initSegment($segment);
        }
    }

    /**
     * get all overviews
     *
     * @return array
     */
    private function generateAllOverviews()
    {
        $configRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig');
        $overviewRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsOverview');
        $segmentRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsSegment');
        $config = $configRepository->findFirst();

        // add overviews if none exist yet
        if (!count($configRepository->findDefaultOverviews($config))) {
            $overviewRepository->addOverviews($config);
        }

        // init all the segments for this config
        $segments = $config->getSegments();
        foreach ($segments as $segment) {
            $segmentRepository->initSegment($segment);
        }
    }
}

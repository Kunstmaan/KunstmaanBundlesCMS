<?php
namespace Kunstmaan\DashboardBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\DashboardBundle\Repository\AnalyticsSegmentRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class GoogleAnalyticsOverviewsGenerateCommand extends ContainerAwareCommand
{
    /** @var EntityManagerInterface $em */
    private $em;

    protected function configure()
    {
        $this
            ->setName('kuma:dashboard:widget:googleanalytics:overviews:generate')
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
        $configId  = false;
        $segmentId = false;
        try {
            $configId  = $input->getOption('config');
            $segmentId = $input->getOption('segment');
        } catch (\Exception $e) {
        }

        try {
            if ($segmentId) {
                $this->generateOverviewsOfSegment($segmentId);
            } else {
                if ($configId) {
                    $this->generateOverviewsOfConfig($configId);
                } else {
                    $this->generateAllOverviews();
                }
            }

            $output->writeln('<fg=green>Overviews succesfully generated</fg=green>');
        } catch (\InvalidArgumentException $e) {
            $output->writeln('<fg=red>' . $e->getMessage() . '</fg=red>');
        }

    }


    /**
     * Get all overviews of a segment
     *
     * @param int $segmentId
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    private function generateOverviewsOfSegment($segmentId)
    {
        /** @var AnalyticsSegmentRepository $segmentRepository */
        $segmentRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsSegment');
        $segment           = $segmentRepository->find($segmentId);

        if (!$segment) {
            throw new \InvalidArgumentException('Unknown segment ID');
        }

        // init the segment
        $segmentRepository->initSegment($segment);
    }

    /**
     * Get all overviews of a config
     *
     * @param int $configId
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    private function generateOverviewsOfConfig($configId)
    {
        $configRepository   = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig');
        $segmentRepository  = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsSegment');
        $overviewRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsOverview');
        // get specified config
        $config = $configRepository->find($configId);

        if (!$config) {
            throw new \InvalidArgumentException('Unknown config ID');
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
        $configRepository   = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig');
        $overviewRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsOverview');
        $segmentRepository  = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsSegment');
        $configs            = $configRepository->findAll();

        foreach ($configs as $config) {
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
}

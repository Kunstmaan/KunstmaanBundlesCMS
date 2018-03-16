<?php

namespace Kunstmaan\DashboardBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\DashboardBundle\Entity\AnalyticsSegment;
use Kunstmaan\DashboardBundle\Repository\AnalyticsSegmentRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GoogleAnalyticsOverviewsGenerateCommand
 */
class GoogleAnalyticsOverviewsGenerateCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManagerInterface
     */
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

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->init();

        // get params
        $configId = false;
        $segmentId = false;
        try {
            $configId = $input->getOption('config');
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
            $output->writeln('<fg=red>'.$e->getMessage().'</fg=red>');
        }
    }

    /**
     * Get all overviews of a segment
     *
     * @param int $segmentId
     *
     * @throws \InvalidArgumentException
     */
    private function generateOverviewsOfSegment($segmentId)
    {
        /** @var AnalyticsSegmentRepository $segmentRepository */
        $segmentRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsSegment');
        /** @var AnalyticsSegment $segment */
        $segment = $segmentRepository->find($segmentId);

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
     * @throws \InvalidArgumentException
     */
    private function generateOverviewsOfConfig($configId)
    {
        $configRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig');
        $segmentRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsSegment');
        $overviewRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsOverview');
        // get specified config
        $config = $configRepository->find($configId);

        if (!$config) {
            throw new \InvalidArgumentException('Unknown config ID');
        }

        // create default overviews for this config if none exist yet
        if (!\count($config->getOverviews())) {
            $overviewRepository->addOverviews($config);
        }

        // init all the segments for this config
        foreach ($config->getSegments() as $segment) {
            $segmentRepository->initSegment($segment);
        }
    }

    /**
     * Get all overviews
     */
    private function generateAllOverviews()
    {
        $configRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig');
        $overviewRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsOverview');
        $segmentRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsSegment');
        $configs = $configRepository->findAll();

        foreach ($configs as $config) {
            // add overviews if none exist yet
            if (!\count($configRepository->findDefaultOverviews($config))) {
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

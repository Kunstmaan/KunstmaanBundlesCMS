<?php

namespace Kunstmaan\DashboardBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\DashboardBundle\Entity\AnalyticsConfig;
use Kunstmaan\DashboardBundle\Entity\AnalyticsOverview;
use Kunstmaan\DashboardBundle\Entity\AnalyticsSegment;
use Kunstmaan\DashboardBundle\Repository\AnalyticsSegmentRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'kuma:dashboard:widget:googleanalytics:overviews:generate', description: 'Generate overviews')]
final class GoogleAnalyticsOverviewsGenerateCommand extends Command
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
            ->addOption('config', null, InputOption::VALUE_OPTIONAL, 'Specify to only update one config', false)
            ->addOption('segment', null, InputOption::VALUE_OPTIONAL, 'Specify to only update one segment', false)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
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
            } elseif ($configId) {
                $this->generateOverviewsOfConfig($configId);
            } else {
                $this->generateAllOverviews();
            }

            $output->writeln('<fg=green>Overviews succesfully generated</fg=green>');

            return 0;
        } catch (\InvalidArgumentException $e) {
            $output->writeln('<fg=red>' . $e->getMessage() . '</fg=red>');

            return 1;
        }
    }

    /**
     * Get all overviews of a segment
     *
     * @param int $segmentId
     *
     * @throws \InvalidArgumentException
     */
    private function generateOverviewsOfSegment($segmentId): void
    {
        /** @var AnalyticsSegmentRepository $segmentRepository */
        $segmentRepository = $this->em->getRepository(AnalyticsSegment::class);
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
    private function generateOverviewsOfConfig($configId): void
    {
        $configRepository = $this->em->getRepository(AnalyticsConfig::class);
        $segmentRepository = $this->em->getRepository(AnalyticsSegment::class);
        $overviewRepository = $this->em->getRepository(AnalyticsOverview::class);
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
        $segments = $config->getSegments();
        foreach ($segments as $segment) {
            $segmentRepository->initSegment($segment);
        }
    }

    /**
     * get all overviews
     */
    private function generateAllOverviews(): void
    {
        $configRepository = $this->em->getRepository(AnalyticsConfig::class);
        $overviewRepository = $this->em->getRepository(AnalyticsOverview::class);
        $segmentRepository = $this->em->getRepository(AnalyticsSegment::class);
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

<?php

namespace Kunstmaan\DashboardBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\DashboardBundle\Entity\AnalyticsConfig;
use Kunstmaan\DashboardBundle\Entity\AnalyticsOverview;
use Kunstmaan\DashboardBundle\Entity\AnalyticsSegment;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'kuma:dashboard:widget:googleanalytics:overviews:list', description: 'List available overviews')]
final class GoogleAnalyticsOverviewsListCommand extends Command
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
            ->addOption('config', null, InputOption::VALUE_OPTIONAL, 'Specify to only list overviews of one config', false)
            ->addOption('segment', null, InputOption::VALUE_OPTIONAL, 'Specify to only list overviews of one segment', false)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // get params
        $configId = $input->getOption('config');
        $segmentId = $input->getOption('segment');

        try {
            if ($segmentId) {
                $overviews = $this->getOverviewsOfSegment($segmentId);
            } elseif ($configId) {
                $overviews = $this->getOverviewsOfConfig($configId);
            } else {
                $overviews = $this->getAllOverviews();
            }

            if (\count($overviews)) {
                $result = "\t" . '<fg=green>' . \count($overviews) . '</fg=green> overviews found:';
                $output->writeln($result);
                foreach ($overviews as $overview) {
                    $result = "\t" . '(id: <fg=cyan>' . $overview->getId() . '</fg=cyan>)';
                    $result .= "\t" . '(config: <fg=cyan>' . $overview->getconfig()->getId() . '</fg=cyan>)';
                    if ($overview->getSegment()) {
                        $result .= "\t" . '(segment: <fg=cyan>' . $overview->getSegment()->getId() . '</fg=cyan>)';
                    } else {
                        $result .= "\t\t";
                    }
                    $result .= "\t" . $overview->getTitle();

                    $output->writeln($result);
                }
            } else {
                $output->writeln('No overviews found');
            }

            return 0;
        } catch (\Exception $e) {
            $output->writeln('<fg=red>' . $e->getMessage() . '</fg=red>');

            return 1;
        }
    }

    /**
     * get all overviews of a segment
     *
     * @param int $segmentId
     */
    private function getOverviewsOfSegment($segmentId): array
    {
        // get specified segment
        $segmentRepository = $this->em->getRepository(AnalyticsSegment::class);
        $segment = $segmentRepository->find($segmentId);

        if (!$segment) {
            throw new \Exception('Unkown segment ID');
        }

        // get the overviews
        return $segment->getOverviews();
    }

    /**
     * get all overviews of a config
     *
     * @param int $configId
     */
    private function getOverviewsOfConfig($configId): array
    {
        // get specified config
        $configRepository = $this->em->getRepository(AnalyticsConfig::class);
        $config = $configRepository->find($configId);

        if (!$config) {
            throw new \Exception('Unkown config ID');
        }

        // get the overviews
        return $config->getOverviews();
    }

    /**
     * get all overviews
     */
    private function getAllOverviews(): array
    {
        // get all overviews
        $overviewRepository = $this->em->getRepository(AnalyticsOverview::class);

        return $overviewRepository->findAll();
    }
}

<?php

namespace Kunstmaan\DashboardBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\DashboardBundle\Entity\AnalyticsConfig;
use Kunstmaan\DashboardBundle\Entity\AnalyticsSegment;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class GoogleAnalyticsSegmentsListCommand extends Command
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
            ->setName('kuma:dashboard:widget:googleanalytics:segments:list')
            ->setDescription('List available segments')
            ->addOption(
                'config',
                null,
                InputOption::VALUE_OPTIONAL,
                'Specify to only list overviews of one config',
                false
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // get params
        $configId = $input->getOption('config');

        try {
            if ($configId) {
                $segments = $this->getSegmentsOfConfig($configId);
            } else {
                $segments = $this->getAllSegments();
            }

            if (\count($segments)) {
                $result = "\t" . '<fg=green>' . \count($segments) . '</fg=green> segments found:';
                $output->writeln($result);
                foreach ($segments as $segment) {
                    $result = "\t" . '(id: <fg=cyan>' . $segment->getId() . '</fg=cyan>)';
                    $result .= "\t" . '(config: <fg=cyan>' . $segment->getconfig()->getId() . '</fg=cyan>)';
                    $result .= "\t" . '<fg=cyan>' . $segment->getquery() . '</fg=cyan> (' . $segment->getName() . ')';

                    $output->writeln($result);
                }
            } else {
                $output->writeln('No segments found');
            }

            return 0;
        } catch (\Exception $e) {
            $output->writeln('<fg=red>' . $e->getMessage() . '</fg=red>');

            return 1;
        }
    }

    /**
     * get all segments of a config
     *
     * @param int $configId
     */
    private function getSegmentsOfConfig($configId): array
    {
        // get specified config
        $configRepository = $this->em->getRepository(AnalyticsConfig::class);
        $config = $configRepository->find($configId);

        if (!$config) {
            throw new \Exception('Unkown config ID');
        }

        // get the segments
        return $config->getSegments();
    }

    private function getAllSegments(): array
    {
        // get all segments
        $segmentRepository = $this->em->getRepository(AnalyticsSegment::class);

        return $segmentRepository->findAll();
    }
}

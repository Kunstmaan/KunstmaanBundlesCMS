<?php
namespace Kunstmaan\DashboardBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class GoogleAnalyticsSegmentListCommand extends ContainerAwareCommand
{
    /** @var EntityManager $em */
    private $em;

    protected function configure()
    {
        $this
            ->setName('ga:segment:list')
            ->setDescription('List available segments')
            ->addOption(
                'config',
                null,
                InputOption::VALUE_OPTIONAL,
                'Specify to only list overviews of one config',
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

        try {
            $segments = array();

            if ($configId) {
                $segments = $this->getSegmentsOfConfig($configId);
            } else {
                $segments = $this->getAllSegments();
            }

            if (count($segments)) {
                $result = "\t".'<fg=green>' . count($segments) . '</fg=green> segments found:';
                $output->writeln($result);
                foreach($segments as $segment) {
                    $result = "\t".'(id: <fg=cyan>' .$segment->getId() . '</fg=cyan>)';
                    $result .= "\t".'(config: <fg=cyan>' .$segment->getconfig()->getId() . '</fg=cyan>)';
                    $result .= "\t" .'<fg=cyan>'. $segment->getquery() .'</fg=cyan> ('.$segment->getName().')';

                    $output->writeln($result);
                }
            } else {
                $output->writeln('No segments found');
            }
        } catch (\Exception $e) {
            $output->writeln('<fg=red>'.$e->getMessage().'</fg=red>');
        }

    }

    /**
     * get all segments of a config
     * @param int $configId
     * @return array
     */
    private function getSegmentsOfConfig($configId)
    {
        // get specified config
        $configRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig');
        $config = $configRepository->find($configId);

        if (!$config) {
            throw new \Exception('Unkown config ID');
        }

        // get the segments
        return $config->getSegments();
    }

    /**
     * get all segments
     *
     * @return array
     */
    private function getAllSegments()
    {
        // get all segments
        $segmentRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsSegment');
        return $segmentRepository->findAll();
    }
}

<?php

namespace Kunstmaan\DashboardBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @final since 5.1
 * NEXT_MAJOR extend from `Command` and remove `$this->getContainer` usages
 */
class GoogleAnalyticsSegmentsListCommand extends ContainerAwareCommand
{
    /** @var EntityManagerInterface $em */
    private $em;

    /**
     * @param EntityManagerInterface|null $em
     */
    public function __construct(/* EntityManagerInterface */ $em = null)
    {
        parent::__construct();

        if (!$em instanceof EntityManagerInterface) {
            @trigger_error(sprintf('Passing a command name as the first argument of "%s" is deprecated since version symfony 3.4 and will be removed in symfony 4.0. If the command was registered by convention, make it a service instead. ', __METHOD__), E_USER_DEPRECATED);

            $this->setName(null === $em ? 'kuma:dashboard:widget:googleanalytics:segments:list' : $em);

            return;
        }

        $this->em = $em;
    }

    protected function configure()
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

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (null === $this->em) {
            $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        }

        // get params
        $configId = $input->getOption('config');

        try {
            $segments = [];

            if ($configId) {
                $segments = $this->getSegmentsOfConfig($configId);
            } else {
                $segments = $this->getAllSegments();
            }

            if (count($segments)) {
                $result = "\t".'<fg=green>' . count($segments) . '</fg=green> segments found:';
                $output->writeln($result);
                foreach ($segments as $segment) {
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
     *
     * @param int $configId
     *
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

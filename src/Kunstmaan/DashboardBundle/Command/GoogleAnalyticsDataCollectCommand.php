<?php
namespace Kunstmaan\DashboardBundle\Command;

use Doctrine\ORM\EntityManager;
use Kunstmaan\DashboardBundle\Command\Helper\Analytics\ChartDataCommandHelper;
use Kunstmaan\DashboardBundle\Command\Helper\Analytics\GoalCommandHelper;
use Kunstmaan\DashboardBundle\Command\Helper\Analytics\MetricsCommandHelper;
use Kunstmaan\DashboardBundle\Command\Helper\Analytics\UsersCommandHelper;
use Kunstmaan\DashboardBundle\Entity\AnalyticsOverview;
use Kunstmaan\DashboardBundle\Repository\AnalyticsConfigRepository;
use Kunstmaan\DashboardBundle\Repository\AnalyticsOverviewRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class GoogleAnalyticsDataCollectCommand extends ContainerAwareCommand
{
    /** @var EntityManager $em */
    private $em;

    /** @var OutputInterface $output */
    private $output;

    /** @var int $errors */
    private $errors = 0;

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('kuma:dashboard:widget:googleanalytics:data:collect')
            ->setDescription('Collect the Google Analytics dashboard widget data')
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
            )
            ->addOption(
                'overview',
                null,
                InputOption::VALUE_OPTIONAL,
                'Specify to only update one overview',
                false
            );
    }

    /**
     * Inits instance variables for global usage.
     *
     * @param OutputInterface $output The output
     */
    private function init($output)
    {
        $this->output = $output;
        $this->serviceHelper = $this->getContainer()->get('kunstmaan_dashboard.helper.google.analytics.service');
        $this->em = $this->getContainer()->get('doctrine')->getManager();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // init
        $this->init($output);

        // check if token is set
        $configHelper = $this->getContainer()->get('kunstmaan_dashboard.helper.google.analytics.config');
        if (!$configHelper->tokenIsSet()) {
            $this->output->writeln('You haven\'t configured a Google account yet');
            return;
        }

        // get params
        $configId = false;
        $segmentId = false;
        $overviewId = false;

        try {
            $configId  = $input->getOption('config');
            $segmentId = $input->getOption('segment');
            $overviewId = $input->getOption('overview');
        } catch (\Exception $e) {}

        // get the overviews
        $overviews = array();
        try {
            if ($overviewId) {
                // get specified overview
                $overviews[] = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsOverview')->getOverview($overviewId);
            } else if ($segmentId) {
                $segmentRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsSegment');

                // get specified segment
                $segment = $segmentRepository->getSegment($segmentId);

                // init the segment
                $segmentRepository->initSegment($segment);

                // get the overviews
                $overviews = $segment->getOverviews();
            } else if ($configId) {
                $configRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig');
                $segmentRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsSegment');

                // get specified config
                $config = $configRepository->getConfig($configId);

                // init all the segments for this config
                $segments = $config->getSegments();
                foreach ($segments as $segment) {
                    $segmentRepository->initSegment($segment);
                }

                // get the overviews
                $overviews = $config->getOverviews();
            } else {
                $configRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig');
                $overviewRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsOverview');
                $segmentRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsSegment');

                $config = $configRepository->getConfig();

                // add overviews if none exist yet
                if (sizeof($config->getOverviews()) == 0) {
                    $overviewRepository->addOverviews($config);
                }

                // init all the segments for this config
                $segments = $config->getSegments();
                foreach ($segments as $segment) {
                    $segmentRepository->initSegment($segment);
                }

                // get all overviews
                $overviews = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsOverview')->getAll();
            }

            $this->updateData($overviews);

            $result = '<fg=green>Google Analytics data updated with <fg=red>'.$this->errors.'</fg=red> error';
            $result .= $this->errors > 1 ? 's</fg=green>' : '</fg=green>';
            $this->output->writeln($result); // done
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
            exit;
        }

    }

    /**
     * update the overviews
     *
     * @param array $overviews collection of all overviews which need to be updated
     */
    public function updateData($overviews)
    {
        // helpers
        $queryHelper = $this->getContainer()->get('kunstmaan_dashboard.helper.google.analytics.query');
        $configHelper = $this->getContainer()->get('kunstmaan_dashboard.helper.google.analytics.config');
        $metrics = new MetricsCommandHelper($configHelper, $queryHelper, $this->output, $this->em);
        $chartData = new ChartDataCommandHelper($configHelper, $queryHelper, $this->output, $this->em);
        $goals = new GoalCommandHelper($configHelper, $queryHelper, $this->output, $this->em);
        $visitors = new UsersCommandHelper($configHelper, $queryHelper, $this->output, $this->em);

        // get data per overview
        foreach ($overviews as $overview) {
            /** @var AnalyticsOverview $overview */
            $this->output->writeln('Fetching data for overview "<fg=green>' . $overview->getTitle() . '</fg=green>"');

            try {
                // metric data
                $metrics->getData($overview);
                if ($overview->getSessions()) { // if there are any visits
                    // day-specific data
                    $chartData->getData($overview);

                    // get goals
                    $goals->getData($overview);

                    // visitor types
                    $visitors->getData($overview);
                } else {
                    // reset overview
                    $this->reset($overview);
                    $this->output->writeln("\t" . 'No visitors');
                }
            // persist entity back to DB
                $this->output->writeln("\t" . 'Persisting..');
                $this->em->persist($overview);
                $this->em->flush();

                $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->setUpdated($overview->getConfig()->getId());
            } catch (\Google_ServiceException $e) {
                $this->output->writeln("\t" . '<fg=red>Invalid segment</fg=red>');
                $this->errors += 1;
            }
        }
    }


    /**
     * Reset the data for the overview
     *
     * @param AnalyticsOverview $overview The overview
     */
    private function reset(AnalyticsOverview $overview)
    {
        // reset overview
        $overview->setNewUsers(0);
        $overview->setReturningUsers(0);
    }
}

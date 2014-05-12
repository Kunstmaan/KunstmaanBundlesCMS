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

class GoogleAnalyticsCommand extends ContainerAwareCommand
{
    /** @var EntityManager $em */
    private $em;

    /** @var OutputInterface $output */
    private $output;

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('kuma:dashboard:widget:googleanalytics')
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
        $configId = $input->getOption('config');
        $segmentId = $input->getOption('segment');
        $overviewId = $input->getOption('overview');

        // get the overviews
        $overviews = array();
        try {
            if ($overviewId) {
                // get specified overview
                $overviews[] = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsOverview')->getOverview($overviewId);
            } else if ($segmentId) {
                // get specified segment
                $segment = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsSegment')->getSegment($segmentId);

                // init the segment
                $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->initSegment($segment);

                // get the overviews
                $overviews = $segment->getOverviews();
            } else if ($configId) {
                $configRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig');

                // get specified config
                $config = $configRepository->getConfig($configId);

                // init all the segments for this config
                $segments = $config->getSegments();
                foreach ($segments as $segment) {
                    $configRepository->initSegment($segment);
                }

                // get the overviews
                $overviews = $config->getOverviews();
            } else {
                // get all overviews
                $overviews = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsOverview')->getAll();
            }


            echo count($overviews);
            exit;

            $this->updateData($overviews);

            $this->output->writeln('Google Analytics data succesfully updated'); // done
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
        // get the query helper
        $queryHelper = $this->getContainer()->get('kunstmaan_dashboard.helper.google.analytics.query');

        // get data per overview
        foreach ($overviews as $overview) {
            /** @var AnalyticsOverview $overview */
            $this->output->writeln('Getting data for overview "' . $overview->getTitle() . '"');

            // metric data
            $metrics = new MetricsCommandHelper($configHelper, $queryHelper, $this->output, $this->em);
            $metrics->getData($overview);

            if ($overview->getSessions()) { // if there are any visits
                // day-specific data
                $chartData = new ChartDataCommandHelper($configHelper, $queryHelper, $this->output, $this->em);
                $chartData->getData($overview);

                // get goals
                $goals = new GoalCommandHelper($configHelper, $queryHelper, $this->output, $this->em);
                $goals->getData($overview);

                // visitor types
                $visitors = new UsersCommandHelper($configHelper, $queryHelper, $this->output, $this->em);
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

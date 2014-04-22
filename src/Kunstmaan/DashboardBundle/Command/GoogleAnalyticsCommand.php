<?php
namespace Kunstmaan\DashboardBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Kunstmaan\DashboardBundle\Helper\GoogleAnalyticsHelper;
use Kunstmaan\DashboardBundle\Helper\GoogleClientHelper;

use Kunstmaan\DashboardBundle\Command\Helper\Analytics\MetricsCommandHelper;
use Kunstmaan\DashboardBundle\Command\Helper\Analytics\TrafficCommandHelper;
use Kunstmaan\DashboardBundle\Command\Helper\Analytics\ChartDataCommandHelper;
use Kunstmaan\DashboardBundle\Command\Helper\Analytics\GoalCommandHelper;
use Kunstmaan\DashboardBundle\Command\Helper\Analytics\VisitorsCommandHelper;
use Kunstmaan\DashboardBundle\Command\Helper\Analytics\TopPagesCommandHelper;
use Kunstmaan\DashboardBundle\Command\Helper\Analytics\TrafficSourcesCommandHelper;

class GoogleAnalyticsCommand extends ContainerAwareCommand {
    /** @var GoogleClientHelper $googleClientHelper */
    private $googleClientHelper;
    /** @var Client $googleClient */
    private $googleClient;
    /** @var GoogleAnalyticsHelper $analyticsHelper */
    private $analyticsHelper;
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
            ->setDescription('Collect the Google Analytics dashboard widget data');
    }

    /**
     * Inits instance variables for global usage.
     *
     * @param OutputInterface $output The output
     */
    private function init($output)
    {
        $this->output = $output;
        $this->googleClientHelper = $this->getContainer()->get('kunstmaan_dashboard.googleclienthelper');
        $this->em = $this->getContainer()->get('doctrine')->getEntityManager();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
       $this->init($output);

        // if no token set yet
        if (!$this->googleClientHelper->tokenIsSet()) {
                $this->output->writeln('You haven\'t configured a Google account yet');
                return;
        }

        // create API Analytics helper to execute queries
        $this->analyticsHelper = $this->getContainer()->get('kunstmaan_dashboard.googleanalyticshelper');
        $this->analyticsHelper->init($this->googleClientHelper);

        // set new update timestamp
        $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->setUpdated();

        // get data for each overview
        $overviews = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsOverview')->getAll();
        foreach ($overviews as $overview) {
            $this->output->writeln('Getting data for overview "' . $overview->getTitle() . '"');

            // metric data
            $metrics = new MetricsCommandHelper($this->analyticsHelper, $this->output, $this->em);
            $metrics->getData($overview);

            if ($overview->getVisits()) { // if there are any visits
                // traffic data
                $traffic = new TrafficCommandHelper($this->analyticsHelper, $this->output, $this->em);
                // $traffic->getData($overview);

                // day-specific data
                $chartData = new ChartDataCommandHelper($this->analyticsHelper, $this->output, $this->em);
                $chartData->getData($overview);

                // get goals
                $goals = new GoalCommandHelper($this->googleClientHelper, $this->analyticsHelper, $this->output, $this->em);
                $goals->getData($overview);

                // visitor types
                $visitors = new VisitorsCommandHelper($this->analyticsHelper, $this->output, $this->em);
                $visitors->getData($overview);

                // top pages
                $topPages = new TopPagesCommandHelper($this->analyticsHelper, $this->output, $this->em);
                $topPages->getData($overview);

                // traffic sources
                $trafficSources = new TrafficSourcesCommandHelper($this->analyticsHelper, $this->output, $this->em);
                $trafficSources->getData($overview);
            } else {
                // reset overview
                $this->reset($overview);
                $this->output->writeln("\t" . 'No visitors');
            }
            // persist entity back to DB
            $this->output->writeln("\t" . 'Persisting..');
            $this->em->persist($overview);
            $this->em->flush();
        }

        $this->output->writeln('Google Analytics data succesfully updated'); // done
    }

    /**
     * Reset the data for the overview
     *
     * @param AnalyticsOverview $overview The overview
     */
    private function reset(&$overview)
    {
        // reset overview
        $overview->setNewVisits(0);
        $overview->setReturningVisits(0);
        $overview->setTrafficDirect(0);
        $overview->setTrafficSearchEngine(0);
        $overview->setTrafficReferral(0);
    }
}

<?php
namespace Kunstmaan\DashboardBundle\Command;

use Doctrine\ORM\EntityManager;
use Kunstmaan\DashboardBundle\Command\Helper\Analytics\ChartDataCommandHelper;
use Kunstmaan\DashboardBundle\Command\Helper\Analytics\GoalCommandHelper;
use Kunstmaan\DashboardBundle\Command\Helper\Analytics\MetricsCommandHelper;
use Kunstmaan\DashboardBundle\Command\Helper\Analytics\UsersCommandHelper;
use Kunstmaan\DashboardBundle\Entity\AnalyticsOverview;
use Kunstmaan\DashboardBundle\Helper\GoogleAnalyticsHelper;
use Kunstmaan\DashboardBundle\Helper\GoogleClientHelper;
use Kunstmaan\DashboardBundle\Repository\AnalyticsConfigRepository;
use Kunstmaan\DashboardBundle\Repository\AnalyticsOverviewRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class GoogleAnalyticsCommand extends ContainerAwareCommand
{
    /** @var GoogleClientHelper $googleClientHelper */
    private $googleClientHelper;
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
            ->setDescription('Collect the Google Analytics dashboard widget data')
            ->addArgument(
                'configId',
                InputArgument::OPTIONAL,
                'Specify to only update one config'
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
        $this->googleClientHelper = $this->getContainer()->get('kunstmaan_dashboard.googleclienthelper');
        $this->em = $this->getContainer()->get('doctrine')->getManager();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->init($output);
        $configId = $input->getArgument('configId') ? $input->getArgument('configId') : false;

        // if no token set yet
        if (!$this->googleClientHelper->tokenIsSet()) {
            $this->output->writeln('You haven\'t configured a Google account yet');
            return;
        }

        // get data for each overview
        if ($configId) {
            $this->googleClientHelper->init($configId);
            try {
                $overviews = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->getConfig($configId)->getOverviews();
            } catch (\Exception $e) {
                $this->output->writeln('Unknown config ID.');
                exit;
            }
        } else {
            $overviews = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsOverview')->getAll();
        }

        // create API Analytics helper to execute queries
        $this->analyticsHelper = $this->getContainer()->get('kunstmaan_dashboard.googleanalyticshelper');
        $this->analyticsHelper->init($this->googleClientHelper);

        foreach ($overviews as $overview) {
            /** @var AnalyticsOverview $overview */
            $this->output->writeln('Getting data for overview "' . $overview->getTitle() . '"');

            // metric data
            $metrics = new MetricsCommandHelper($this->analyticsHelper, $this->output, $this->em);
            $metrics->getData($overview);

            if ($overview->getSessions()) { // if there are any visits
                // day-specific data
                $chartData = new ChartDataCommandHelper($this->analyticsHelper, $this->output, $this->em);
                $chartData->getData($overview);

                // get goals
                $goals = new GoalCommandHelper($this->googleClientHelper, $this->analyticsHelper, $this->output, $this->em);
                $goals->getData($overview);

                // visitor types
                $visitors = new UsersCommandHelper($this->analyticsHelper, $this->output, $this->em);
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
        }

        // set new update timestamp
        /** @var AnalyticsConfigRepository $analyticsConfigRepository */
        $analyticsConfigRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig');
        $analyticsConfigRepository->setUpdated($configId);

        $this->output->writeln('Google Analytics data succesfully updated'); // done
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

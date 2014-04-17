<?php

namespace Kunstmaan\DashboardBundle\Command;

use Kunstmaan\DashboardBundle\Entity\AnalyticsTopReferral;
use Kunstmaan\DashboardBundle\Entity\AnalyticsTopSearch;
use Kunstmaan\DashboardBundle\Entity\AnalyticsTopPage;
use Kunstmaan\DashboardBundle\Entity\AnalyticsGoal;
use Kunstmaan\DashboardBundle\Entity\AnalyticsCampaign;
use Kunstmaan\DashboardBundle\Helper\GoogleAnalyticsHelper;
use Kunstmaan\DashboardBundle\Helper\GoogleClientHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Symfony CLI command to update the analytics data using app/console kuma:ga:update
 */
class UpdateAnalyticsOverviewCommand extends ContainerAwareCommand
{

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
                    ->setName('kuma:ga:update')
                    ->setDescription('Update Google Analytics overviews');
        }

        /**
         * Executes the current command.
         *
         * @param InputInterface  $input  The input
         * @param OutputInterface $output The output
         *
         * @return int
         */
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
                    $this->getMetrics($overview);

                    if ($overview->getVisits()) { // if there are any visits
                        // day-specific data
                        $this->getTrafficTypes($overview);

                        // day-specific data
                        $this->getChartData($overview);

                        // get goals
                        $this->getGoals($overview);

                        // visitor types
                        $this->getVisitorTypes($overview);

                        // top pages
                        $this->getTopPages($overview);

                        // traffic sources
                        $this->getTrafficSources($overview);


                        // unused, please keep here just in case if some of this data is still needed in the future

                        // // bounce rate
                        // $this->getBounceRate($overview);

                        // // top referrals
                        // $this->getTopReferrals($overview);

                        // // top searches
                        // $this->getTopSearches($overview);

                        // // top campaigns
                        // $this->getTopCampaigns($overview);
                    } else { // if no visits
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
         * Inits instance variables for global usage.
         *
         * @param OutputInterface $output The output
         */
        private function init($output)
        {
                $this->output = $output;

                // get API client
                $this->googleClientHelper = $this->getContainer()->get('kunstmaan_dashboard.googleclienthelper');

                // setup entity manager
                $this->em = $this->getContainer()->get('doctrine')->getEntityManager();
        }

        /**
         * Fetch normal metric data and set it for the overview
         *
         * @param AnalyticsOverview $overview The overview
         */
        private function getMetrics(&$overview)
        {
            $this->output->writeln("\t" . 'Fetching metrics..');

            $results = $this->analyticsHelper->getResults(
                $overview->getTimespan(),
                $overview->getStartOffset(),
                'ga:visits, ga:visitors, ga:pageviews, ga:pageviewsPerSession, ga:avgSessionDuration'
            );
            $rows    = $results->getRows();

            // visits metric
            $visits  = is_numeric($rows[0][0]) ? $rows[0][0] : 0;
            $overview->setVisits($visits);

            // visits metric
            $visitors  = is_numeric($rows[0][1]) ? $rows[0][1] : 0;
            $overview->setVisitors($visitors);

            // pageviews metric
            $pageviews = is_numeric($rows[0][2]) ? $rows[0][2] : 0;
            $overview->setPageViews($pageviews);

            // pages per visit metric
            $pagesPerVisit = is_numeric($rows[0][3]) ? $rows[0][3] : 0;
            $overview->setPagesPerVisit($pagesPerVisit);

            // avg visit duration metric
            $avgVisitDuration = is_numeric($rows[0][4]) ? $rows[0][4] : 0;
            $overview->setAvgVisitDuration($avgVisitDuration);

        }

        /**
         * Fetch traffic type data and set it for the overview
         *
         * @param AnalyticsOverview $overview The overview
         */
        private function getTrafficTypes(&$overview)
        {
            $this->output->writeln("\t" . 'Fetching traffic types..');

            // mobile
            $results = $this->analyticsHelper->getResults(
                $overview->getTimespan(),
                $overview->getStartOffset(),
                'ga:visitors',
                array('segment' => 'gaid::-14')
            );
            $rows    = $results->getRows();

            $mobileTraffic = is_numeric($rows[0][0]) ? $rows[0][0] : 0;

            // tablet
            $results = $this->analyticsHelper->getResults(
                $overview->getTimespan(),
                $overview->getStartOffset(),
                'ga:visitors',
                array('segment' => 'gaid::-13')
            );
            $rows    = $results->getRows();

            $tabletTraffic = is_numeric($rows[0][0]) ? $rows[0][0] : 0;

            // desktop
            $dekstopTraffic = $overview->getVisitors() - ($mobileTraffic + $tabletTraffic);
            $overview->setMobileTraffic($mobileTraffic);
            $overview->setTabletTraffic($tabletTraffic);
            $overview->setDesktopTraffic($dekstopTraffic);
        }

        /**
         * Fetch chart data and set it for the overview
         *
         * @param AnalyticsOverview $overview The overview
         */
        private function getChartData(&$overview)
        {
            $this->output->writeln("\t" . 'Fetching chart data..');

            // create the right timespan
            $timespan = $overview->getTimespan() - $overview->getStartOffset();
            if ($timespan <= 1) {
                $extra = array(
                    'dimensions' => 'ga:hour',
                    'sort' => 'ga:hour'
                    );
            } else if ($timespan <= 7) {
                $extra = array(
                    'dimensions' => 'ga:dayOfWeekName'
                    );
            } else if ($timespan <= 93) {
                $extra = array(
                    'dimensions' => 'ga:yearWeek',
                    'sort' => 'ga:yearWeek'
                    );
            } else {
                $extra = array(
                    'dimensions' => 'ga:yearMonth',
                    'sort' => 'ga:yearMonth'
                    );
            }

            // get visits
            $results = $this->analyticsHelper->getResults(
                $overview->getTimespan(),
                $overview->getStartOffset(),
                'ga:visits',
                $extra
            );
            $rows    = $results->getRows();

            $chartData = array();
            foreach ($rows as $row) {
                if ($timespan <= 1) {
                    $timestamp = $row[0] . 'h';
                } else if ($timespan <= 7) {
                    $timestamp = $row[0];
                } else if ($timespan <= 93) {
                    $timestamp = strtotime(substr($row[0], 0, 4) . 'W' . substr($row[0], 4, 2));
                    $timestamp = date('d/m/Y', $timestamp);
                } else {
                    $timestamp = substr($row[0], 4, 2) . '/' . substr($row[0], 0, 4);
                }

                $chartData['visits'][] = array('timestamp' => $timestamp, 'visits' => $row[1]);
            }

            // get visitors
            $results = $this->analyticsHelper->getResults(
                $overview->getTimespan(),
                $overview->getStartOffset(),
                'ga:visitors',
                $extra
            );
            $rows    = $results->getRows();

            foreach ($rows as $row) {
                if ($timespan <= 1) {
                    $timestamp = $row[0] . 'h';
                } else if ($timespan <= 7) {
                    $timestamp = substr($row[0], 6, 2) . '-' . substr($row[0], 4, 2) . '-' . substr($row[0], 0, 4);
                } else if ($timespan <= 93) {
                    $timestamp = strtotime(substr($row[0], 0, 4) . 'W' . substr($row[0], 4, 2));
                    $timestamp = date('d/m/Y', $timestamp);
                } else {
                    $timestamp = substr($row[0], 4, 2) . '/' . substr($row[0], 0, 4);
                }

                $chartData['visitors'][] = array('timestamp' => $timestamp, 'visits' => $row[1]);
            }

            // adding data to the overview
            $overview->setChartData(json_encode($chartData, JSON_UNESCAPED_SLASHES));
        }

        /**
         * Fetch visitor type data and set it for the overview
         *
         * @param AnalyticsOverview $overview The overview
         */
        private function getVisitorTypes(&$overview)
        {
                // visitor types
                $this->output->writeln("\t" . 'Fetching visitor types..');
                $results = $this->analyticsHelper->getResults(
                    $overview->getTimespan(),
                    $overview->getStartOffset(),
                    'ga:visits',
                    array('dimensions' => 'ga:visitorType')
                );
                $rows    = $results->getRows();

                // new visitors
                $data = is_array($rows) && isset($rows[0][1]) ? $rows[0][1] : 0;
                $overview->setNewVisits($data);

                // returning visitors
                $data = is_array($rows) && isset($rows[1][1]) ? $rows[1][1] : 0;
                $overview->setReturningVisits($data);
        }

        /**
         * Fetch traffic source data and set it for the overview
         *
         * @param AnalyticsOverview $overview The overview
         */
        private function getTrafficSources(&$overview)
        {
                // traffic sources
                $this->output->writeln("\t" . 'Fetching traffic sources..');
                $results = $this->analyticsHelper->getResults(
                    $overview->getTimespan(),
                    $overview->getStartOffset(),
                    'ga:visits',
                    array('dimensions' => 'ga:medium', 'sort' => 'ga:medium')
                );
                $rows    = $results->getRows();

                // resetting default values
                $overview->setTrafficDirect(0);
                $overview->setTrafficSearchEngine(0);
                $overview->setTrafficReferral(0);

                if (is_array($rows)) {
                    foreach ($rows as $row) {
                        switch ($row[0]) {
                            case '(none)': // direct traffic
                                $overview->setTrafficDirect($row[1]);
                                break;

                            case 'organic': // search engine traffic
                                $overview->setTrafficSearchEngine($row[1]);
                                break;

                            case 'referral': // referral traffic
                                $overview->setTrafficReferral($row[1]);
                                break;

                            default:
                                // TODO other referral types?
                                // cfr. https://developers.google.com/analytics/devguides/reporting/core/dimsmets#view=detail&group=traffic_sources&jump=ga_medium
                                break;
                        }
                    }
                }
        }


        /**
         * Fetch bounce rate data and set it for the overview
         *
         * @param AnalyticsOverview $overview The overview
         */
        private function getBounceRate(&$overview)
        {
            $this->output->writeln("\t" . 'Fetching bounce rate..');

            // bounce rate
            $results = $this->analyticsHelper->getResults(
                $overview->getTimespan(),
                $overview->getStartOffset(),
                'ga:visitBounceRate'
            );
            $rows    = $results->getRows();
            $visits  = is_numeric($rows[0][0]) ? $rows[0][0] : 0;
            $overview->setBounceRate($visits);
        }

        /**
         * Fetch referral data and set it for the overview
         *
         * @param AnalyticsOverview $overview The overview
         */
        private function getTopReferrals(&$overview)
        {
                // top referral sites
                $this->output->writeln("\t" . 'Fetching referral sites..');
                $results = $this->analyticsHelper->getResults(
                    $overview->getTimespan(),
                    $overview->getStartOffset(),
                    'ga:visits',
                    array(
                        'dimensions'  => 'ga:source',
                        'sort'        => '-ga:visits',
                        'filters'     => 'ga:medium==referral',
                        'max-results' => '3'
                    )
                );
                $rows    = $results->getRows();

                // delete existing entries
                if (is_array($overview->getReferrals()->toArray())) {
                        foreach ($overview->getReferrals()->toArray() as $referral) {
                                $this->em->remove($referral);
                        }
                        $this->em->flush();
                }

                // load new referrals
                if (is_array($rows)) {
                        foreach ($rows as $row) {
                                $referral = new AnalyticsTopReferral();
                                $referral->setName($row[0]);
                                $referral->setVisits($row[1]);
                                $referral->setOverview($overview);
                                $overview->getReferrals()->add($referral);
                        }
                }
        }

        /**
         * Fetch search terms data and set it for the overview
         *
         * @param AnalyticsOverview $overview The overview
         */
        private function getTopSearches(&$overview)
        {
                // top searches
                $this->output->writeln("\t" . 'Fetching searches..');
                $results = $this->analyticsHelper->getResults(
                    $overview->getTimespan(),
                    $overview->getStartOffset(),
                    'ga:searchVisits',
                    array(
                        'dimensions' => 'ga:searchKeyword',
                        'sort' => '-ga:searchVisits',
                        'max-results' => '3'
                    )
                );
                $rows    = $results->getRows();

                // delete existing entries
                if (is_array($overview->getSearches()->toArray())) {
                        foreach ($overview->getSearches()->toArray() as $search) {
                                $this->em->remove($search);
                        }
                        $this->em->flush();
                }

                // load new searches
                if (is_array($rows)) {
                        foreach ($rows as $key => $row) {
                                $search = new AnalyticsTopSearch();
                                $search->setName($row[0]);
                                $search->setVisits($row[1]);
                                $search->setOverview($overview);
                                $overview->getSearches()->add($search);
                        }
                }

        }

        /**
         * Fetch page data and set it for the overview
         *
         * @param AnalyticsOverview $overview The overview
         */
        private function getTopPages(&$overview)
        {
                // top pages
                $this->output->writeln("\t" . 'Fetching top pages..');
                $results = $this->analyticsHelper->getResults(
                    $overview->getTimespan(),
                    $overview->getStartOffset(),
                    'ga:pageviews',
                    array(
                        'dimensions'=>'ga:pagePath',
                        'sort'=>'-ga:pageviews',
                        'max-results' => '10'
                    )
                );
                $rows    = $results->getRows();

                // delete existing entries
                if (is_array($overview->getPages()->toArray())) {
                    foreach ($overview->getPages()->toArray() as $page) {
                        $this->em->remove($page);
                    }
                    $this->em->flush();
                }

                // load new referrals
                if (is_array($rows)) {
                    foreach ($rows as $row) {
                        $referral = new AnalyticsTopPage();
                        $referral->setName($row[0]);
                        $referral->setVisits($row[1]);
                        $referral->setOverview($overview);
                        $overview->getPages()->add($referral);
                    }
                }
        }

        /**
         * Fetch campaign data and set it for the overview
         *
         * @param AnalyticsOverview $overview The overview
         */
        private function getTopCampaigns(&$overview)
        {
            // top campaigns
            $this->output->writeln("\t" . 'Fetching campaigns..');
            $results = $this->analyticsHelper->getResults(
                $overview->getTimespan(),
                $overview->getStartOffset(),
                'ga:visits',
                array(
                    'dimensions' => 'ga:campaign',
                    'sort' => '-ga:visits',
                    'max-results' => '4'
                )
            );
            $rows    = $results->getRows();
            // first entry is '(not set)' and not needed
            unset($rows[0]);

            // delete existing entries
            if (is_array($overview->getCampaigns()->toArray())) {
                foreach ($overview->getCampaigns()->toArray() as $campaign) {
                    $this->em->remove($campaign);
                }
                $this->em->flush();
            }

            // load new campaigns
            if (is_array($rows)) {
                foreach ($rows as $key => $row) {
                    $campaign = new AnalyticsCampaign();
                    $campaign->setName($row[0]);
                    $campaign->setVisits($row[1]);
                    $campaign->setOverview($overview);
                    $overview->getCampaigns()->add($campaign);
                }
            }

        }

        /**
         * Fetch all goals
         *
         * @param AnalyticsOverview $overview The overview
         */
        private function getGoals(&$overview)
        {
            // goals
            $this->output->writeln("\t" . 'Fetching goals..');

            // calculate timespan
            $timespan = $overview->getTimespan() - $overview->getStartOffset();
            if ($timespan <= 1) {
                $extra = array(
                    'dimensions' => 'ga:hour',
                    'sort' => 'ga:hour'
                    );
            } else if ($timespan <= 7) {
                $extra = array(
                    'dimensions' => 'ga:dayOfWeekName'
                    );
            } else if ($timespan <= 93) {
                $extra = array(
                    'dimensions' => 'ga:yearWeek',
                    'sort' => 'ga:yearWeek'
                    );
            } else {
                $extra = array(
                    'dimensions' => 'ga:yearMonth',
                    'sort' => 'ga:yearMonth'
                    );
            }

            // delete existing entries
            if (is_array($overview->getGoals()->toArray())) {
                foreach ($overview->getGoals()->toArray() as $goal) {
                        $this->em->remove($goal);
                }
                $this->em->flush();
            }

            // get goals
            $goals = $this->analyticsHelper->getAnalytics()
                            ->management_goals
                            ->listManagementGoals($this->googleClientHelper->getAccountId(), $this->googleClientHelper->getPropertyId(), $this->googleClientHelper->getProfileId())
                            ->items;

            // add new goals
            if (is_array($goals)) {
                foreach ($goals as $key=>$value) {
                    $this->getGoal($overview, $key+1, $value, $extra);
                }
            }
        }

        /**
         * Fetch a specific goals
         *
         * @param AnalyticsOverview $overview The overview
         */
        private function getGoal(&$overview, $key, $value, $extra) {
            // fetch a goal
            $this->output->writeln("\t\t" . 'Fetching goal '.$key.': "'.$value->name.'"');

            $goal = new AnalyticsGoal();
            $timespan = $overview->getTimespan() - $overview->getStartOffset();

            // create the query
            $results = $this->analyticsHelper->getResults(
                $overview->getTimespan(),
                $overview->getStartOffset(),
                'ga:goal'.$key.'Completions',
                $extra
            );
            $rows    = $results->getRows();

            // parse the results
            $chartData = array();
            $visits = 0;

            foreach($rows as $row) {
                // total visit count
                $visits += $row[1];

                if ($timespan <= 1) {
                    $timestamp = $row[0] . 'h';
                } else if ($timespan <= 7) {
                    $timestamp = substr($row[0], 6, 2) . '-' . substr($row[0], 4, 2) . '-' . substr($row[0], 0, 4);
                } else if ($timespan <= 93) {
                    $timestamp = strtotime(substr($row[0], 0, 4) . 'W' . substr($row[0], 4, 2));
                    $timestamp = date('d/m/Y', $timestamp);
                } else {
                    $timestamp = substr($row[0], 4, 2) . '/' . substr($row[0], 0, 4);
                }

                $chartData[] = array('timestamp' => $timestamp, 'visits' => $row[1]);
            }

            // set the data
            $goal->setVisits($visits);
            $goal->setChartData(json_encode($chartData));
            $goal->setOverview($overview);
            $goal->setName($value->name);
            $goal->setPosition($key);
            $overview->getGoals()->add($goal);
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

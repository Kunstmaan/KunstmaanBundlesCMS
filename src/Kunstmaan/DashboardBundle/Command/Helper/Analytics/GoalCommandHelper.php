<?php
namespace Kunstmaan\DashboardBundle\Command\Helper\Analytics;

use Doctrine\ORM\EntityManager;
use Kunstmaan\DashboardBundle\Entity\AnalyticsGoal;
use Kunstmaan\DashboardBundle\Entity\AnalyticsOverview;
use Kunstmaan\DashboardBundle\Helper\GoogleAnalyticsHelper;
use Kunstmaan\DashboardBundle\Helper\GoogleClientHelper;
use Symfony\Component\Console\Output\OutputInterface;

class GoalCommandHelper extends AbstractAnalyticsCommandHelper
{
    /** @var GoogleClientHelper $googleClientHelper */
    private $googleClientHelper;

    /**
     * Constructor
     *
     * @param GoogleClientHelper $googleClientHelper
     * @param GoogleAnalyticsHelper $analyticsHelper
     * @param OutputInterface $output
     * @param EntityManager $em
     */
    public function __construct($googleClientHelper, $analyticsHelper, $output, $em)
    {
        parent::__construct($analyticsHelper, $output, $em);
        $this->googleClientHelper = $googleClientHelper;
    }


    /**
     * @return array
     */
    private function getAllGoals(){
        // Get the goals from the saved profile. These are a maximum of 20 goals.
        $goals = $this->analyticsHelper->getAnalytics()
            ->management_goals
            ->listManagementGoals($this->googleClientHelper->getAccountId(), $this->googleClientHelper->getPropertyId(), $this->googleClientHelper->getProfileId())
            ->items;

        if (is_array($goals)){
            return $goals;
        }
        return false;
    }

    /**
     * @return array
     */
    private function prepareMetrics(){
        $goals = $this->getAllGoals();
        if (!$goals) {
            return false;
        }

        $metrics = array();
        foreach ($goals as $key => $value) {
            $metrics[] = 'ga:goal' . ($key+1) . 'Completions';
        }
        // Create the metric parameter string, there is a limit of 10 metrics per query, and a max of 20 goals available.
        if (count($metrics) <= 10) {
            $part1 = implode(',', $metrics);
            return array($part1);
        } else {
            $part1 = implode(',', array_slice($metrics, 0, 10));
            $part2 = implode(',', array_slice($metrics, 10, 10));
            return array($part1, $part2);
        }
    }

    /**
     * @param AnalyticsOverview $overview
     * @param $metrics
     * @param $dimensions
     * @return mixed
     */
    private function requestGoalResults(AnalyticsOverview $overview, $metrics, $dimensions){
        // Execute query
        if ($overview->getUseYear()) {
            $begin = date('Y-m-d', mktime(0, 0, 0, 1, 1, date('Y')));
            $end = date('Y-m-d', mktime(0, 0, 0, 1, 1, date('Y', strtotime('+1 year'))));

            $results = $this->analyticsHelper->getResultsByDate(
                $begin,
                $end,
                $metrics,
                $dimensions
            );
        } else {
            $results = $this->analyticsHelper->getResults(
                $overview->getTimespan(),
                $overview->getStartOffset(),
                $metrics,
                $dimensions
            );
        }
        return $results->getRows();
    }

    /**
     * get data and save it for the overview
     *
     * @param AnalyticsOverview $overview The overview
     */
    public function getData(&$overview)
    {
        $this->output->writeln("\t" . 'Fetching goals..');

        // calculate timespan
        $timespan = $overview->getTimespan() - $overview->getStartOffset();
        if ($timespan <= 1) {
            $extra = array('dimensions' => 'ga:date,ga:hour');
            $start = 2;
        } else if ($timespan <= 7) {
            $extra = array('dimensions' => 'ga:date,ga:hour');
            $start = 2;
        } else if ($timespan <= 31) {
            $extra = array('dimensions' => 'ga:week,ga:day,ga:date');
            $start = 3;
        } else {
            $extra = array('dimensions' => 'ga:isoYearIsoWeek');
            $start = 1;
        }

        $goals = $this->getAllGoals();
        if (!$goals) {
            foreach ($overview->getGoals() as $goal) {
                $this->em->remove($goal);
            }
            $this->em->flush();
            return;
        }
        $goaldata = array();

        // Create an array with for each goal an entry to create the metric parameter.
        foreach ($goals as $key => $value) {
            $key++;
            $goaldata[] = array('position' => $key, 'name' => $value->name);
        }

        $metrics = $this->prepareMetrics();

        $rows = $this->requestGoalResults($overview, $metrics[0], $extra);
        // Execute an extra query if there are more than 10 goals to query
        if (sizeof($metrics) > 1) {
            $rows2 = $this->requestGoalResults($overview, $metrics[1], $extra);

            for ($i = 0; $i < sizeof($rows2); $i++) {
                // Merge the results of the extra query data with the previous query data.
                $rows[$i] = array_merge($rows[$i], array_slice($rows2[$i], $start, sizeof($rows2) - $start));
            }
        }

        // Create a result array to be parsed and create Goal objects from
        $goalCollection = array();
        for ($i = 0; $i < sizeof($goaldata); $i++) {
            $goalEntry = array();
            foreach ($rows as $row) {
                // Create a timestamp for each goal visit (this depends on the timespan of the overview: split per hour, day, week, month)
                if ($timespan <= 1) {
                    $timestamp = mktime($row[1], 0, 0, substr($row[0], 4, 2), substr($row[0], 6, 2), substr($row[0], 0, 4));
                    $timestamp = date('Y-m-d H:00', $timestamp);
                } else if ($timespan <= 7) {
                    $timestamp = mktime($row[1], 0, 0, substr($row[0], 4, 2), substr($row[0], 6, 2), substr($row[0], 0, 4));
                    $timestamp = date('Y-m-d H:00', $timestamp);
                } else if ($timespan <= 31) {
                    $timestamp = mktime(0, 0, 0, substr($row[2], 4, 2), substr($row[2], 6, 2), substr($row[2], 0, 4));
                    $timestamp = date('Y-m-d H:00', $timestamp);
                } else {
                    $timestamp = strtotime(substr($row[0], 0, 4) . 'W' . substr($row[0], 4, 2));
                    $timestamp = date('Y-m-d H:00', $timestamp);
                }
                $goalEntry[$timestamp] = $row[$i + $start];
            }
            $goalCollection['goal' . $goaldata[$i]['position']]['name'] = $goaldata[$i]['name'];
            $goalCollection['goal' . $goaldata[$i]['position']]['position'] = $goaldata[$i]['position'];
            $goalCollection['goal' . $goaldata[$i]['position']]['visits'] = $goalEntry;
        }

        // Parse the goals and append them to the overview.
        $this->parseGoals($overview, $goalCollection);
    }


    /**
     * Fetch a specific goals
     *
     * @param AnalyticsOverview $overview The overview
     * @param $goalCollection
     */
    private function parseGoals(&$overview, $goalCollection)
    {

        // delete existing entries
        foreach ($overview->getGoals() as $goal) {
            $this->em->remove($goal);
        }
        $this->em->flush();

        foreach ($goalCollection as $goalEntry) {
            // create a new goal
            $goal = new AnalyticsGoal();
            $goal->setOverview($overview);
            $goal->setName($goalEntry['name']);
            $goal->setPosition($goalEntry['position']);

            $chartData = array();
            $totalVisits = 0;
            // Fill the chartdata array
            foreach ($goalEntry['visits'] as $timestamp => $visits) {
                $totalVisits += $visits;
                $chartData[] = array('timestamp' => $timestamp, 'conversions' => $visits);
            }

            // set the data
            $goal->setVisits($totalVisits);
            $goal->setChartData(json_encode($chartData));

            $goals = $overview->getGoals();
            $goals[] = $goal;

            $this->output->writeln("\t\t" . 'Fetched goal ' . $goal->getPosition() . ': "' . $goal->getName() . '" @ ' . $totalVisits);
        }
    }
}

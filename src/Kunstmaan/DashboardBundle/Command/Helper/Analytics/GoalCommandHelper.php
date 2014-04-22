<?php
namespace Kunstmaan\DashboardBundle\Command\Helper\Analytics;

use Kunstmaan\DashboardBundle\Command\Helper\Analytics\AbstractAnalyticsCommandHelper;
use Kunstmaan\DashboardBundle\Entity\AnalyticsGoal;

class GoalCommandHelper extends AbstractAnalyticsCommandHelper
{
    /** @var GoogleClientHelper $googleClientHelper */
    private $googleClientHelper;

    /**
     * Constructor
     *
     * @param GoogleClientHelper    $googleClientHelper
     * @param GooglaAnalytisHelper  $analyticsHelper
     * @param OutputInterface       $output
     * @param EntityManager         $em
     */
    public function __construct($googleClientHelper, $analyticsHelper, $output, $em)
    {
        parent::__construct($analyticsHelper, $output, $em);
        $this->googleClientHelper = $googleClientHelper;
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
        $start = 0;
        if ($timespan <= 1) {
            $extra = array( 'dimensions' => 'ga:date,ga:hour' );
            $start = 2;
        } else if ($timespan <= 7) {
            $extra = array( 'dimensions' => 'ga:date,ga:hour' );
            $start = 2;
        } else if ($timespan <= 31) {
            $extra = array( 'dimensions' => 'ga:week,ga:day,ga:date' );
            $start = 3;
        } else {
            $extra = array( 'dimensions' => 'ga:isoYearIsoWeek' );
            $start = 1;
        }

        // Get the goals from the saved profile. These are a maximum of 20 goals.
        $goals = $this->analyticsHelper->getAnalytics()
                        ->management_goals
                        ->listManagementGoals($this->googleClientHelper->getAccountId(), $this->googleClientHelper->getPropertyId(), $this->googleClientHelper->getProfileId())
                        ->items;

        if (is_array($goals)) {
            $metrics = array();
            $goal = array();

            // Create an array with for each goal an entry to create the metric parameter.
            foreach ($goals as $key=>$value) {
                $key++;
                $metrics[] = 'ga:goal'.$key.'Completions';
                $goaldata[] = array('position'=>$key, 'name'=>$value->name);
            }

            // Create the metric parameter string, there is a limit of 10 metrics per query, and a max of 20 goals available.
            if (count($metrics)<=10) {
                $part1 = implode(',', $metrics);
                $part2 = false;
            } else {
                $part1 = implode(',', array_slice($metrics, 0, 10));
                $part2 = implode(',', array_slice($metrics, 10, 10));
            }

            // Execute query
            $results = $this->analyticsHelper->getResults(
                $overview->getTimespan(),
                $overview->getStartOffset(),
                $part1,
                $extra
            );
            $rows = $results->getRows();

            // Execute an extra query if there are more than 10 goals to query
            if ($part2) {
                $results = $this->analyticsHelper->getResults(
                    $overview->getTimespan(),
                    $overview->getStartOffset(),
                    $part2,
                    $extra
                );
                $rows2 = $results->getRows();
                for ($i = 0; $i < sizeof($rows2); $i++) {
                    // Merge the results of the extra query data with the previous query data.
                    $rows[$i] = array_merge($rows[$i], array_slice($rows2[$i], $start, sizeof($rows2)-$start));
                }
            }

            // Create a good result array to be parsed and create Goal objects from
            $goalCollection = array();
            for ($i = $start; $i < sizeof($rows[0]); $i++) {
                $goalEntry = array();
                foreach($rows as $row) {
                    // Create a timestamp for each goal visit (this depends on the timespan of the overview: split per hour, day, week, month)
                    if ($timespan <= 1) {
                        $timestamp = mktime($row[1], 0, 0, substr($row[0], 4, 2), substr($row[0], 6, 2), substr($row[0], 0, 4));
                        $timestamp = date('Y-m-d H:00', $timestamp);
                    } else if ($timespan <= 7) {
                        $timestamp = mktime($row[1], 0, 0, substr($row[0], 4, 2), substr($row[0], 6, 2), substr($row[0], 0, 4));
                        $timestamp = date('Y-m-d H:00', $timestamp);
                    } else if ($timespan <= 31) {
                        $timestamp = mktime(0, 0, 0, substr($row[0], 4, 2), substr($row[2], 6, 2), substr($row[2], 0, 4));
                        $timestamp = date('Y-m-d H:00', $timestamp);
                    } else {
                        $timestamp = strtotime(substr($row[0], 0, 4).'W'.substr($row[0], 4, 2));
                        $timestamp = date('Y-m-d H:00', $timestamp);
                    }
                    $goalEntry[$timestamp] = $row[$i];
                }
                $goalCollection['goal'.$goaldata[$i-$start]['position']]['name'] = $goaldata[$i-$start]['name'];
                $goalCollection['goal'.$goaldata[$i-$start]['position']]['position'] = $goaldata[$i-$start]['position'];
                $goalCollection['goal'.$goaldata[$i-$start]['position']]['visits'] = $goalEntry;
            }

            // Parse the goals and append them to the overview.
            $this->parseGoals($overview, $goalCollection);
        }
    }


    /**
     * Fetch a specific goals
     *
     * @param AnalyticsOverview $overview The overview
     */
    private function parseGoals(&$overview, $goalCollection) {

        // delete existing entries
        if (is_array($overview->getGoals()->toArray())) {
            foreach ($overview->getGoals()->toArray() as $goal) {
                    $this->em->remove($goal);
            }
            $this->em->flush();
        }

        foreach($goalCollection as $goalEntry) {
            // create a new goal
            $goal = new AnalyticsGoal();
            $goal->setOverview($overview);
            $goal->setName($goalEntry['name']);
            $goal->setPosition($goalEntry['position']);
            $this->output->writeln("\t\t" . 'Fetching goal '.$goal->getPosition().': "'.$goal->getName().'"');

            $count = 0;
            $chartData = array();
            $totalVisits = 0;
            $steps = ceil(sizeof($goalEntry['visits'])/10);
            $chartEntryVisits = 0;
            // Fill the chartdata array
            foreach ($goalEntry['visits'] as $timestamp => $visits) {
                $count++;
                $totalVisits += $visits;
                $chartEntryVisits += $visits;

                if ($count%$steps == 0) {
                    $chartData[] = array('timestamp' => $timestamp, 'visits' => $chartEntryVisits);
                    $count = 0;
                    $chartEntryVisits = 0;
                }
            }

            // set the data
            $goal->setVisits($totalVisits);
            $goal->setChartData(json_encode($chartData));
            $overview->getGoals()->add($goal);
        }
    }
}

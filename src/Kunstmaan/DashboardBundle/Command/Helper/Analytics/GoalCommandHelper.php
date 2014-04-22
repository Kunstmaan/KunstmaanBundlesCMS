<?php
namespace Kunstmaan\DashboardBundle\Command\Helper\Analytics;

use Kunstmaan\DashboardBundle\Command\Helper\Analytics\AbstractAnalyticsCommandHelper;
use Kunstmaan\DashboardBundle\Entity\AnalyticsGoal;

class GoalCommandHelper extends AbstractAnalyticsCommandHelper {

    private $googleClientHelper;

    public function __construct($googleClientHelper, $analyticsHelper, $output, $em) {
        parent::__construct($analyticsHelper, $output, $em);
        $this->googleClientHelper = $googleClientHelper;
    }

    // TODO: clean code
    public function getData(&$overview) {
        // goals
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

        // get goals
        $goals = $this->analyticsHelper->getAnalytics()
                        ->management_goals
                        ->listManagementGoals($this->googleClientHelper->getAccountId(), $this->googleClientHelper->getPropertyId(), $this->googleClientHelper->getProfileId())
                        ->items;

        // add new goals
        if (is_array($goals)) {
            $metrics = array();
            $goal = array();

            foreach ($goals as $key=>$value) {
                $key++;
                $metrics[] = 'ga:goal'.$key.'Completions';
                $goaldata[] = array('position'=>$key, 'name'=>$value->name);
            }

            if (count($metrics)<=10) {
                $part1 = implode(',', $metrics);
                $part2 = false;
            } else {
                $part1 = implode(',', array_slice($metrics, 0, 10));
                $part2 = implode(',', array_slice($metrics, 10, 10));
            }

             // create the query
            $results = $this->analyticsHelper->getResults(
                $overview->getTimespan(),
                $overview->getStartOffset(),
                $part1,
                $extra
            );
            $rows = $results->getRows();

            if ($part2) {
                $results = $this->analyticsHelper->getResults(
                    $overview->getTimespan(),
                    $overview->getStartOffset(),
                    $part2,
                    $extra
                );
                $rows2 = $results->getRows();
                for ($i = 0; $i < sizeof($rows2); $i++) {
                    $rows[$i] = array_merge($rows[$i], array_slice($rows2[$i], $start, sizeof($rows2)-$start));
                }
            }

            $goalCollection = array();
            for ($i = $start; $i < sizeof($rows[0]); $i++) {
                $goalEntry = array();
                foreach($rows as $row) {
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

            $this->addGoals($overview, $goalCollection);

        }
    }


    /**
     * Fetch a specific goals
     *
     * @param AnalyticsOverview $overview The overview
     */
    private function addGoals(&$overview, $goalCollection) {

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

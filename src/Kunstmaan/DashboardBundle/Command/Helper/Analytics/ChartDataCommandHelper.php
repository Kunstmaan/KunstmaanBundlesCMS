<?php
namespace Kunstmaan\DashboardBundle\Command\Helper\Analytics;


use Kunstmaan\DashboardBundle\Entity\AnalyticsOverview;

class ChartDataCommandHelper extends AbstractAnalyticsCommandHelper
{

    /**
     * get data and save it for the overview
     *
     * @param AnalyticsOverview $overview The overview
     */
    public function getData(&$overview)
    {
        $this->output->writeln("\t" . 'Fetching chart data..');

        // create the right timespan
        $timespan = $overview->getTimespan() - $overview->getStartOffset();
        if ($timespan <= 1) {
            $extra = array(
                'dimensions' => 'ga:date,ga:hour'
            );
        } else if ($timespan <= 7) {
            $extra = array(
                'dimensions' => 'ga:date,ga:hour'
            );
        } else if ($timespan <= 31) {
            $extra = array(
                'dimensions' => 'ga:week,ga:day,ga:date'
            );
        } else {
            $extra = array(
                'dimensions' => 'ga:isoYearIsoWeek'
            );
        }

        // get visits & visitors
        if ($overview->getUseYear()) {
            $begin = date('Y-m-d', mktime(0, 0, 0, 1, 1, date('Y')));
            $end = date('Y-m-d', strtotime("-1 days"));
            $results = $this->query->getResultsByDate(
                $begin,
                $end,
                'ga:sessions, ga:users, ga:newUsers, ga:pageviews',
                $extra
            );
        } else {
            $results = $this->query->getResults(
                $overview->getTimespan(),
                $overview->getStartOffset(),
                'ga:sessions, ga:users, ga:newUsers, ga:pageviews',
                $extra
            );
        }

        $rows = $results->getRows();

        $chartData = array();
        $totalUsers = 0;
        $totalSessions = 0;
        $totalPageviews = 0;
        $chartDataMaxValue = 0;

        foreach ($rows as $row) {
            // metrics
            $sessions = $row[sizeof($row) - 4];
            $users = $row[sizeof($row) - 3];
            $newusers = $row[sizeof($row) - 2];
            $pageviews = $row[sizeof($row) - 1];

            // set max chartdata value
            if ($chartDataMaxValue < $pageviews) {
                $chartDataMaxValue = $pageviews;
            }

            // timestamp
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

            // add to chart array
            $chartEntry = array(
                'timestamp' => $timestamp,
                'sessions' => $sessions,
                'users' => $users,
                'newusers' => $newusers,
                'pageviews' => $pageviews

            );
            $chartData[] = $chartEntry;
        }

        // adding data to the overview
        $overview->setChartDataMaxValue($chartDataMaxValue);
        $overview->setChartData(json_encode($chartData, JSON_NUMERIC_CHECK));
    }

}

<?php
namespace Kunstmaan\DashboardBundle\Command\Helper\Analytics;

use Kunstmaan\DashboardBundle\Command\Helper\Analytics\AbstractAnalyticsCommandHelper;

class ChartDataCommandHelper extends AbstractAnalyticsCommandHelper {

    /**
     * get data and save it for the overview
     *
     * @param AnalyticsOverview $overview The overview
     */
    public function getData(&$overview) {
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
        $results = $this->analyticsHelper->getResults(
            $overview->getTimespan(),
            $overview->getStartOffset(),
            'ga:sessions, ga:users, ga:newUsers, ga:pageviews',
            $extra
        );
        $rows    = $results->getRows();
        $chartData = array();
        foreach ($rows as $row) {
            // chart data
            if ($timespan <= 1) {
                $timestamp = mktime($row[1], 0, 0, substr($row[0], 4, 2), substr($row[0], 6, 2), substr($row[0], 0, 4));
                $timestamp = date('Y-m-d H:00', $timestamp);
                $chartData['visits'][] = array('timestamp' => $timestamp, 'visits' => $row[2]);
                $chartData['visitors'][] = array('timestamp' => $timestamp, 'visits' => $row[3]);
                $chartData['newusers'][] = array('timestamp' => $timestamp, 'visits' => $row[4]);
                $chartData['pageviews'][] = array('timestamp' => $timestamp, 'visits' => $row[5]);
            } else if ($timespan <= 7) {
                $timestamp = mktime($row[1], 0, 0, substr($row[0], 4, 2), substr($row[0], 6, 2), substr($row[0], 0, 4));
                $timestamp = date('Y-m-d H:00', $timestamp);
                $chartData['visits'][] = array('timestamp' => $timestamp, 'visits' => $row[2]);
                $chartData['visitors'][] = array('timestamp' => $timestamp, 'visits' => $row[3]);
                $chartData['newusers'][] = array('timestamp' => $timestamp, 'visits' => $row[4]);
                $chartData['pageviews'][] = array('timestamp' => $timestamp, 'visits' => $row[5]);
            } else if ($timespan <= 31) {
                $timestamp = mktime(0, 0, 0, substr($row[2], 4, 2), substr($row[2], 6, 2), substr($row[2], 0, 4));
                $timestamp = date('Y-m-d H:00', $timestamp);
                $chartData['visits'][] = array('timestamp' => $timestamp, 'visits' => $row[3]);
                $chartData['visitors'][] = array('timestamp' => $timestamp, 'visits' => $row[4]);
                $chartData['newusers'][] = array('timestamp' => $timestamp, 'visits' => $row[5]);
                $chartData['pageviews'][] = array('timestamp' => $timestamp, 'visits' => $row[6]);
            } else {
                $timestamp = strtotime(substr($row[0], 0, 4).'W'.substr($row[0], 4, 2));
                $timestamp = date('Y-m-d H:00', $timestamp);
                $chartData['visits'][] = array('timestamp' => $timestamp, 'visits' => $row[1]);
                $chartData['visitors'][] = array('timestamp' => $timestamp, 'visits' => $row[2]);
                $chartData['newusers'][] = array('timestamp' => $timestamp, 'visits' => $row[3]);
                $chartData['pageviews'][] = array('timestamp' => $timestamp, 'visits' => $row[4]);
            }
        }


        // adding data to the overview
        $overview->setChartData(json_encode($chartData, JSON_UNESCAPED_SLASHES));
    }

}

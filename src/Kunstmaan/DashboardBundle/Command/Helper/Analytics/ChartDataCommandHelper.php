<?php

namespace Kunstmaan\DashboardBundle\Command\Helper\Analytics;

use Kunstmaan\DashboardBundle\Entity\AnalyticsOverview;

/**
 * Class ChartDataCommandHelper
 */
class ChartDataCommandHelper extends AbstractAnalyticsCommandHelper
{
    /**
     * @param AnalyticsOverview $overview
     *
     * @return array
     */
    protected function getExtra(AnalyticsOverview $overview)
    {
        $timespan = $overview->getTimespan() - $overview->getStartOffset();
        $extra = parent::getExtra($overview);

        if ($timespan <= 1) {
            $extra['dimensions'] = 'ga:date,ga:hour';
        } else {
            if ($timespan <= 7) {
                $extra['dimensions'] = 'ga:date,ga:hour';
            } else {
                if ($timespan <= 31) {
                    $extra['dimensions'] = 'ga:week,ga:day,ga:date';
                } else {
                    $extra['dimensions'] = 'ga:isoYearIsoWeek';
                }
            }
        }

        return $extra;
    }

    /**
     * @param $overview
     *
     * @throws \Exception
     */
    public function getData(AnalyticsOverview $overview)
    {
        $this->output->writeln("\t".'Fetching chart data..');

        // execute the query
        $metrics = 'ga:sessions, ga:users, ga:newUsers, ga:pageviews';
        $rows = $this->executeQuery($overview, $metrics);

        $chartData = [];
        $chartDataMaxValue = 0;
        $timespan = $overview->getTimespan() - $overview->getStartOffset();
        foreach ($rows as $row) {
            // metrics
            $sessions = $row[\count($row) - 4];
            $users = $row[\count($row) - 3];
            $newusers = $row[\count($row) - 2];
            $pageviews = $row[\count($row) - 1];

            $maxvalue = max($sessions, $users, $newusers, $pageviews);
            // set max chartdata value
            if ($chartDataMaxValue < $maxvalue) {
                $chartDataMaxValue = $maxvalue;
            }

            // timestamp
            if ($timespan <= 1) {
                $timestamp = mktime($row[1], 0, 0, substr($row[0], 4, 2), substr($row[0], 6, 2), substr($row[0], 0, 4));
                $timestamp = date('Y-m-d H:00', $timestamp);
            } else {
                if ($timespan <= 7) {
                    $timestamp = mktime($row[1], 0, 0, substr($row[0], 4, 2), substr($row[0], 6, 2), substr($row[0], 0, 4));
                    $timestamp = date('Y-m-d H:00', $timestamp);
                } else {
                    if ($timespan <= 31) {
                        $timestamp = mktime(0, 0, 0, substr($row[2], 4, 2), substr($row[2], 6, 2), substr($row[2], 0, 4));
                        $timestamp = date('Y-m-d H:00', $timestamp);
                    } else {
                        $timestamp = strtotime(substr($row[0], 0, 4).'W'.substr($row[0], 4, 2));
                        $timestamp = date('Y-m-d H:00', $timestamp);
                    }
                }
            }

            // add to chart array
            $chartEntry = [
                'timestamp' => $timestamp,
                'sessions' => $sessions,
                'users' => $users,
                'newusers' => $newusers,
                'pageviews' => $pageviews,

            ];
            $chartData[] = $chartEntry;
        }

        // adding data to the overview
        $overview->setChartDataMaxValue($chartDataMaxValue);
        $overview->setChartData(json_encode($chartData, JSON_NUMERIC_CHECK));
    }
}

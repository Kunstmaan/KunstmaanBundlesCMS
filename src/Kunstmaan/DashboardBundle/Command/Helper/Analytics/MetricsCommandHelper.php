<?php

namespace Kunstmaan\DashboardBundle\Command\Helper\Analytics;


use Kunstmaan\DashboardBundle\Entity\AnalyticsOverview;

/**
 * Class MetricsCommandHelper
 */
class MetricsCommandHelper extends AbstractAnalyticsCommandHelper
{
    /**
     * Get data and save it for the overview
     *
     * @param AnalyticsOverview $overview
     *
     * @throws \Exception
     */
    public function getData(AnalyticsOverview $overview)
    {
        $this->output->writeln("\t".'Fetching metrics..');

        // execute the query
        $metrics = 'ga:sessions, ga:users, ga:pageviews, ga:pageviewsPerSession, ga:avgSessionDuration';
        $rows = $this->executeQuery($overview, $metrics);

        // sessions metric
        $visits = is_numeric($rows[0][0]) ? $rows[0][0] : 0;
        $overview->setSessions($visits);

        // users metric
        $visitors = is_numeric($rows[0][1]) ? $rows[0][1] : 0;
        $overview->setUsers($visitors);

        // pageviews metric
        $pageviews = is_numeric($rows[0][2]) ? $rows[0][2] : 0;
        $overview->setPageViews($pageviews);

        // pages per visit metric
        $pagesPerVisit = is_numeric($rows[0][3]) ? $rows[0][3] : 0;
        $overview->setPagesPerSession($pagesPerVisit);

        // avg visit duration metric
        $avgVisitDuration = is_numeric($rows[0][4]) ? $rows[0][4] : 0;
        $overview->setAvgSessionDuration(gmdate('H:i:s', $avgVisitDuration));
    }

}

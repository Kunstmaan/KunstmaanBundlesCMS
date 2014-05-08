<?php
namespace Kunstmaan\DashboardBundle\Command\Helper\Analytics;


use Kunstmaan\DashboardBundle\Entity\AnalyticsOverview;

class MetricsCommandHelper extends AbstractAnalyticsCommandHelper
{

    /**
     * get data and save it for the overview
     *
     * @param AnalyticsOverview $overview The overview
     */
    public function getData(&$overview)
    {
        $this->output->writeln("\t" . 'Fetching metrics..');

        $gaMetrics = 'ga:sessions, ga:users, ga:pageviews, ga:pageviewsPerSession, ga:avgSessionDuration';
        if ($overview->getUseYear()) {
            $results = $this->analyticsHelper->getResultsByDate(
                date('Y-m-d', mktime(0, 0, 0, 1, 1, date('Y'))),
                date('Y-m-d', strtotime("-1 days")),
                $gaMetrics
            );
        } else {
            $results = $this->analyticsHelper->getResults(
                $overview->getTimespan(),
                $overview->getStartOffset(),
                $gaMetrics
            );
        }

        $rows = $results->getRows();

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
        $overview->setAvgSessionDuration(gmdate("H:i:s", $avgVisitDuration));
    }

}

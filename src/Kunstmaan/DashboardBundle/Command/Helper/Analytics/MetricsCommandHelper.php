<?php
namespace Kunstmaan\DashboardBundle\Command\Helper\Analytics;

use Kunstmaan\DashboardBundle\Command\Helper\Analytics\AbstractAnalyticsCommandHelper;

class MetricsCommandHelper extends AbstractAnalyticsCommandHelper {

    /**
     * get data and save it for the overview
     *
     * @param AnalyticsOverview $overview The overview
     */
    public function getData(&$overview) {
        $this->output->writeln("\t" . 'Fetching metrics..');

        $results = $this->analyticsHelper->getResults(
            $overview->getTimespan(),
            $overview->getStartOffset(),
            'ga:sessions, ga:users, ga:pageviews, ga:pageviewsPerSession, ga:avgSessionDuration'
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

}

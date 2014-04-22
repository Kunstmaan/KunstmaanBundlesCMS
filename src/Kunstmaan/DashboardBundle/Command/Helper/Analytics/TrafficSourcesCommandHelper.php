<?php
namespace Kunstmaan\DashboardBundle\Command\Helper\Analytics;

use Kunstmaan\DashboardBundle\Command\Helper\Analytics\AbstractAnalyticsCommandHelper;

class TrafficSourcesCommandHelper extends AbstractAnalyticsCommandHelper {

    public function getData(&$overview) {
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

}

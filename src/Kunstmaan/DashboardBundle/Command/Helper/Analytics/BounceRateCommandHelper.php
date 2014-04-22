<?php
namespace Kunstmaan\DashboardBundle\Command\Helper\Analytics;

use Kunstmaan\DashboardBundle\Command\Helper\Analytics\AbstractAnalyticsCommandHelper;

class BounceRateCommandHelper extends AbstractAnalyticsCommandHelper {

    public function getData(&$overview) {
        $this->output->writeln("\t" . 'Fetching bounce rate..');

        // bounce rate
        $results = $this->analyticsHelper->getResults(
            $overview->getTimespan(),
            $overview->getStartOffset(),
            'ga:visitBounceRate'
        );
        $rows    = $results->getRows();
        $visits  = is_numeric($rows[0][0]) ? $rows[0][0] : 0;
        $overview->setBounceRate($visits);
    }


}

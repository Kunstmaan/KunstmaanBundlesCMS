<?php
namespace Kunstmaan\DashboardBundle\Command\Helper\Analytics;

use Kunstmaan\DashboardBundle\Command\Helper\Analytics\AbstractAnalyticsCommandHelper;

class VisitorsCommandHelper extends AbstractAnalyticsCommandHelper {

    public function getData(&$overview) {
        // visitor types
        $this->output->writeln("\t" . 'Fetching visitor types..');
        $results = $this->analyticsHelper->getResults(
            $overview->getTimespan(),
            $overview->getStartOffset(),
            'ga:visits',
            array('dimensions' => 'ga:visitorType')
        );
        $rows    = $results->getRows();

        // new visitors
        $data = is_array($rows) && isset($rows[0][1]) ? $rows[0][1] : 0;
        $overview->setNewVisits($data);

        // returning visitors
        $data = is_array($rows) && isset($rows[1][1]) ? $rows[1][1] : 0;
        $overview->setReturningVisits($data);
    }


}

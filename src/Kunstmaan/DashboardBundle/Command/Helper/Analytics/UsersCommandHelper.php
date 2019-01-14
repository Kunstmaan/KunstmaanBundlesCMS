<?php

namespace Kunstmaan\DashboardBundle\Command\Helper\Analytics;

use Kunstmaan\DashboardBundle\Entity\AnalyticsOverview;

class UsersCommandHelper extends AbstractAnalyticsCommandHelper
{
    /**
     * get data and save it for the overview
     *
     * @param AnalyticsOverview $overview The overview
     */
    public function getData(&$overview)
    {
        // visitor types
        $this->output->writeln("\t" . 'Fetching visitor types..');

        // execute the query
        $metrics = 'ga:percentNewSessions';
        $rows = $this->executeQuery($overview, $metrics);

        // new sessions
        $newUsers = is_array($rows) && isset($rows[0][0]) ? $rows[0][0] : 0;
        $overview->setNewUsers($newUsers);
    }
}

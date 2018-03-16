<?php

namespace Kunstmaan\DashboardBundle\Command\Helper\Analytics;

use Kunstmaan\DashboardBundle\Entity\AnalyticsOverview;

/**
 * Class UsersCommandHelper
 */
class UsersCommandHelper extends AbstractAnalyticsCommandHelper
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
        // visitor types
        $this->output->writeln("\t".'Fetching visitor types..');

        // execute the query
        $metrics = 'ga:percentNewSessions';
        $rows = $this->executeQuery($overview, $metrics);

        // new sessions
        $newUsers = \is_array($rows) && isset($rows[0][0]) ? $rows[0][0] : 0;
        $overview->setNewUsers($newUsers);
    }


}

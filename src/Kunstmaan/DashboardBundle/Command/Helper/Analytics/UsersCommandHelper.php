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
        $timestamps = $this->getTimestamps($overview);

        // add segment
        $extra = array();
        if ($overview->getSegment()) {
            $extra['segment'] = $overview->getSegment()->getQuery();
        }

        // execute query
        $results = $this->query->getResultsByDate(
            $timestamps['begin'],
            $timestamps['end'],
            'ga:percentNewSessions',
            $extra
        );

        $rows = $results->getRows();

        // new sessions
        $newUsers = is_array($rows) && isset($rows[0][0]) ? $rows[0][0] : 0;
        $overview->setNewUsers($newUsers);
    }


}

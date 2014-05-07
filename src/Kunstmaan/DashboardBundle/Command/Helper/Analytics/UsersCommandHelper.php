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

        // add segment
        $extra = array();
        if ($overview->getSegment()) {
            $extra['segment'] = $overview->getSegment()->getQuery();
        }

        if ($overview->getUseYear()) {
            $begin = date('Y-m-d', mktime(0, 0, 0, 1, 1, date('Y')));
            $end = date('Y-m-d', mktime(0, 0, 0, 1, 1, date('Y', strtotime('+1 year'))));
            $results = $this->query->getResultsByDate(
                $begin,
                $end,
                'ga:percentNewSessions',
                $extra
            );
        } else {
            $results = $this->query->getResults(
                $overview->getTimespan(),
                $overview->getStartOffset(),
                'ga:percentNewSessions',
                $extra
            );
        }


        $rows = $results->getRows();

        // new sessions
        $newUsers = is_array($rows) && isset($rows[0][0]) ? $rows[0][0] : 0;
        $overview->setNewUsers($newUsers);
    }


}

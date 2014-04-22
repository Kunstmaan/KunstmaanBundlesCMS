<?php
namespace Kunstmaan\DashboardBundle\Command\Helper\Analytics;

use Kunstmaan\DashboardBundle\Command\Helper\Analytics\AbstractAnalyticsCommandHelper;

class TrafficCommandHelper extends AbstractAnalyticsCommandHelper {

    /**
     * get data and save it for the overview
     *
     * @param AnalyticsOverview $overview The overview
     */
    public function getData(&$overview) {
        $this->output->writeln("\t" . 'Fetching traffic types..');

        // mobile
        $results = $this->analyticsHelper->getResults(
            $overview->getTimespan(),
            $overview->getStartOffset(),
            'ga:visitors',
            array('segment' => 'gaid::-14')
        );
        $rows    = $results->getRows();

        $mobileTraffic = is_numeric($rows[0][0]) ? $rows[0][0] : 0;

        // tablet
        $results = $this->analyticsHelper->getResults(
            $overview->getTimespan(),
            $overview->getStartOffset(),
            'ga:visitors',
            array('segment' => 'gaid::-13')
        );
        $rows    = $results->getRows();

        $tabletTraffic = is_numeric($rows[0][0]) ? $rows[0][0] : 0;

        // desktop
        $dekstopTraffic = $overview->getVisitors() - ($mobileTraffic + $tabletTraffic);
        $overview->setMobileTraffic($mobileTraffic);
        $overview->setTabletTraffic($tabletTraffic);
        $overview->setDesktopTraffic($dekstopTraffic);
    }

}

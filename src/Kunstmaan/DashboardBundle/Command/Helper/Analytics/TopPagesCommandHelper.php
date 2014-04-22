<?php
namespace Kunstmaan\DashboardBundle\Command\Helper\Analytics;

use Kunstmaan\DashboardBundle\Command\Helper\Analytics\AbstractAnalyticsCommandHelper;
use Kunstmaan\DashboardBundle\Entity\AnalyticsTopPage;

class TopPagesCommandHelper extends AbstractAnalyticsCommandHelper {

    public function getData(&$overview) {
        // top pages
        $this->output->writeln("\t" . 'Fetching top pages..');
        $results = $this->analyticsHelper->getResults(
            $overview->getTimespan(),
            $overview->getStartOffset(),
            'ga:pageviews',
            array(
                'dimensions'=>'ga:pagePath',
                'sort'=>'-ga:pageviews',
                'max-results' => '10'
            )
        );
        $rows    = $results->getRows();

        // delete existing entries
        if (is_array($overview->getPages()->toArray())) {
            foreach ($overview->getPages()->toArray() as $page) {
                $this->em->remove($page);
            }
            $this->em->flush();
        }

        // load new referrals
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $referral = new AnalyticsTopPage();
                $referral->setName($row[0]);
                $referral->setVisits($row[1]);
                $referral->setOverview($overview);
                $overview->getPages()->add($referral);
            }
        }
    }


}

<?php
namespace Kunstmaan\DashboardBundle\Command\Helper\Analytics;

use Kunstmaan\DashboardBundle\Command\Helper\Analytics\AbstractAnalyticsCommandHelper;
use Kunstmaan\DashboardBundle\Entity\AnalyticsTopReferral;

class TopRefferalsCommandHelper extends AbstractAnalyticsCommandHelper {

    public function getData(&$overview) {
        // top referral sites
        $this->output->writeln("\t" . 'Fetching referral sites..');
        $results = $this->analyticsHelper->getResults(
            $overview->getTimespan(),
            $overview->getStartOffset(),
            'ga:visits',
            array(
                'dimensions'  => 'ga:source',
                'sort'        => '-ga:visits',
                'filters'     => 'ga:medium==referral',
                'max-results' => '3'
            )
        );
        $rows    = $results->getRows();

        // delete existing entries
        if (is_array($overview->getReferrals()->toArray())) {
            foreach ($overview->getReferrals()->toArray() as $referral) {
                $this->em->remove($referral);
            }
            $this->em->flush();
        }

        // load new referrals
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $referral = new AnalyticsTopReferral();
                $referral->setName($row[0]);
                $referral->setVisits($row[1]);
                $referral->setOverview($overview);
                $overview->getReferrals()->add($referral);
            }
        }
    }


}

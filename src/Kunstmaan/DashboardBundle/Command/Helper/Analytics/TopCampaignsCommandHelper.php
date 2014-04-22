<?php
namespace Kunstmaan\DashboardBundle\Command\Helper\Analytics;

use Kunstmaan\DashboardBundle\Command\Helper\Analytics\AbstractAnalyticsCommandHelper;
use Kunstmaan\DashboardBundle\Entity\AnalyticsCampaign;

class TopRefferalsCommandHelper extends AbstractAnalyticsCommandHelper {

    public function getData(&$overview) {
        // top campaigns
        $this->output->writeln("\t" . 'Fetching campaigns..');
        $results = $this->analyticsHelper->getResults(
            $overview->getTimespan(),
            $overview->getStartOffset(),
            'ga:visits',
            array(
                'dimensions' => 'ga:campaign',
                'sort' => '-ga:visits',
                'max-results' => '4'
            )
        );
        $rows    = $results->getRows();
        // first entry is '(not set)' and not needed
        unset($rows[0]);

        // delete existing entries
        if (is_array($overview->getCampaigns()->toArray())) {
            foreach ($overview->getCampaigns()->toArray() as $campaign) {
                $this->em->remove($campaign);
            }
            $this->em->flush();
        }

        // load new campaigns
        if (is_array($rows)) {
            foreach ($rows as $key => $row) {
                $campaign = new AnalyticsCampaign();
                $campaign->setName($row[0]);
                $campaign->setVisits($row[1]);
                $campaign->setOverview($overview);
                $overview->getCampaigns()->add($campaign);
            }
        }
    }


}

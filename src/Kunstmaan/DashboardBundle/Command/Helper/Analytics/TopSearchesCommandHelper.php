<?php
namespace Kunstmaan\DashboardBundle\Command\Helper\Analytics;

use Kunstmaan\DashboardBundle\Command\Helper\Analytics\AbstractAnalyticsCommandHelper;
use Kunstmaan\DashboardBundle\Entity\AnalyticsTopSearch;

class TopSearchesCommandHelper extends AbstractAnalyticsCommandHelper {

    public function getData(&$overview) {
        // top searches
        $this->output->writeln("\t" . 'Fetching searches..');
        $results = $this->analyticsHelper->getResults(
            $overview->getTimespan(),
            $overview->getStartOffset(),
            'ga:searchVisits',
            array(
                'dimensions' => 'ga:searchKeyword',
                'sort' => '-ga:searchVisits',
                'max-results' => '3'
            )
        );
        $rows    = $results->getRows();

        // delete existing entries
        if (is_array($overview->getSearches()->toArray())) {
                foreach ($overview->getSearches()->toArray() as $search) {
                        $this->em->remove($search);
                }
                $this->em->flush();
        }

        // load new searches
        if (is_array($rows)) {
                foreach ($rows as $key => $row) {
                        $search = new AnalyticsTopSearch();
                        $search->setName($row[0]);
                        $search->setVisits($row[1]);
                        $search->setOverview($overview);
                        $overview->getSearches()->add($search);
                }
        }
    }


}

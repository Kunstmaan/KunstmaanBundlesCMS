<?php

namespace Kunstmaan\DashboardBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Kunstmaan\DashboardBundle\Entity\AnalyticsConfig;
use Kunstmaan\DashboardBundle\Entity\AnalyticsOverview;
use Kunstmaan\DashboardBundle\Entity\AnalyticsSegment;

class AnalyticsOverviewRepository extends EntityRepository
{
    /**
     * Get then default overviews (without a segment)
     *
     * @return array
     */
    public function getDefaultOverviews($config = false)
    {
        $em = $this->getEntityManager();
        $dql = 'SELECT o FROM KunstmaanDashboardBundle:AnalyticsOverview o WHERE o.segment IS NULL';
        if ($config) {
            $dql .= " AND o.config = $config";
        }

        $query = $em->createQuery($dql);

        return $query->getResult();
    }

    /**
     * Add overviews for a config and optionally a segment
     *
     * @param AnalyticsConfig  $config
     * @param AnalyticsSegment $segment
     */
    public function addOverviews(&$config, &$segment = null)
    {
        $em = $this->getEntityManager();

        $today = new AnalyticsOverview();
        $today->setTitle('dashboard.ga.tab.today');
        $today->setTimespan(0);
        $today->setStartOffset(0);
        $today->setConfig($config);
        $today->setSegment($segment);
        $em->persist($today);

        $yesterday = new AnalyticsOverview();
        $yesterday->setTitle('dashboard.ga.tab.yesterday');
        $yesterday->setTimespan(1);
        $yesterday->setStartOffset(1);
        $yesterday->setConfig($config);
        $yesterday->setSegment($segment);
        $em->persist($yesterday);

        $week = new AnalyticsOverview();
        $week->setTitle('dashboard.ga.tab.last_7_days');
        $week->setTimespan(7);
        $week->setStartOffset(1);
        $week->setConfig($config);
        $week->setSegment($segment);
        $em->persist($week);

        $month = new AnalyticsOverview();
        $month->setTitle('dashboard.ga.tab.last_30_days');
        $month->setTimespan(30);
        $month->setStartOffset(1);
        $month->setConfig($config);
        $month->setSegment($segment);
        $em->persist($month);

        $year = new AnalyticsOverview();
        $year->setTitle('dashboard.ga.tab.last_12_months');
        $year->setTimespan(365);
        $year->setStartOffset(1);
        $year->setConfig($config);
        $year->setSegment($segment);
        $em->persist($year);

        $yearToDate = new AnalyticsOverview();
        $yearToDate->setTitle('dashboard.ga.tab.year_to_date');
        $yearToDate->setTimespan(365);
        $yearToDate->setStartOffset(1);
        $yearToDate->setConfig($config);
        $yearToDate->setSegment($segment);
        $yearToDate->setUseYear(true);
        $em->persist($yearToDate);

        $em->flush();
    }
}

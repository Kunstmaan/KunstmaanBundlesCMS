<?php

namespace Kunstmaan\DashboardBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Kunstmaan\DataFixtures\Entity\AnalyticsOverview;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Fixture for creating the analytics overviews
 */
class AnalyticsOverviewFixtures extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        $today = new AnalyticsOverview();
        $today->setTitle('Today');
        $today->setTimespan(0);
        $today->setStartOffset(0);
        $em->persist($today);

        $yesterday = new AnalyticsOverview();
        $yesterday->setTitle('Yesterday');
        $yesterday->setTimespan(2);
        $yesterday->setStartOffset(1);
        $em->persist($yesterday);

        $week = new AnalyticsOverview();
        $week->setTitle('Last week');
        $week->setTimespan(7);
        $week->setStartOffset(0);
        $em->persist($week);

        $month = new AnalyticsOverview();
        $month->setTitle('Last month');
        $month->setTimespan(31);
        $month->setStartOffset(0);
        $em->persist($month);

        $year = new AnalyticsOverview();
        $year->setTitle('Last year');
        $year->setTimespan(365);
        $year->setStartOffset(0);
        $em->persist($year);

        $yearToDate = new AnalyticsOverview();
        $yearToDate->setTitle('Year to date');
        $yearToDate->setTimespan(365);
        $yearToDate->setStartOffset(0);
        $yearToDate->setUseYear(true);
        $em->persist($yearToDate);

        $em->flush();

        $this->addReference('today', $today);
        $this->addReference('yesterday', $yesterday);
        $this->addReference('week', $week);
        $this->addReference('month', $month);
        $this->addReference('year', $year);
        $this->addReference('yearToDate', $yearToDate);
    }


    /**
     * Get the order of this fixture
     *
     * @return int
     */
    public function getOrder()
    {
        return 4;
    }
}

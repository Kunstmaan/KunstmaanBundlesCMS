<?php

namespace Kunstmaan\DashboardBundle\Tests\Entity;

use Kunstmaan\DashboardBundle\Entity\AnalyticsGoal;
use Kunstmaan\DashboardBundle\Entity\AnalyticsOverview;
use PHPUnit\Framework\TestCase;

class AnalyticsOverviewTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $goal = new AnalyticsGoal();
        $goal->setVisits(500);

        $entity = new AnalyticsOverview();
        $entity->setTitle('Donald Trump');
        $entity->setConfig(5);
        $entity->setStartOffset(6);
        $entity->setTimespan(7);
        $entity->setSessions(8);
        $entity->setUsers(9);
        $entity->setPageviews(10);
        $entity->setPagesPerSession(11);
        $entity->setAvgSessionDuration(12);
        $entity->setUseYear(2017);
        $entity->setChartDataMaxValue(13);
        $entity->setSegment(14);
        $entity->setReturningUsers(15);
        $entity->setNewUsers(1.23);
        $entity->setChartData([]);
        $entity->setGoals([$goal]);

        $this->assertSame('Donald Trump', $entity->getTitle());
        $this->assertSame(5, $entity->getConfig());
        $this->assertSame(6, $entity->getStartOffset());
        $this->assertSame(7, $entity->getTimespan());
        $this->assertSame(8, $entity->getSessions());
        $this->assertSame(9, $entity->getUsers());
        $this->assertSame(10, $entity->getPageviews());
        $this->assertSame(11, $entity->getPagesPerSession());
        $this->assertSame(12, $entity->getAvgSessionDuration());
        $this->assertSame(2017, $entity->getUseYear());
        $this->assertSame(13, $entity->getChartDataMaxValue());
        $this->assertSame(14, $entity->getSegment());
        $this->assertSame(15, $entity->getReturningUsers());
        $this->assertSame(1.23, $entity->getNewUsers());
        $this->assertIsArray($entity->getChartData());
        $this->assertIsArray($entity->getGoals());
        $this->assertSame(188, $entity->getReturningUsersPercentage());
        $this->assertSame(15.0, $entity->getNewUsersPercentage());
        $this->assertIsArray($entity->getActiveGoals());
    }
}

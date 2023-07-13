<?php

namespace Kunstmaan\DashboardBundle\Tests\Entity;

use Kunstmaan\DashboardBundle\Entity\AnalyticsGoal;
use PHPUnit\Framework\TestCase;

class AnalyticsGoalTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $entity = new AnalyticsGoal();
        $entity->setId(666);
        $entity->setOverview(5);
        $entity->setPosition(6);
        $entity->setName('Donald Trump');
        $entity->setVisits(7);
        $entity->setChartData('blahblah');

        $this->assertSame(666, $entity->getId());
        $this->assertSame(5, $entity->getOverview());
        $this->assertSame(6, $entity->getPosition());
        $this->assertSame('Donald Trump', $entity->getName());
        $this->assertSame(7, $entity->getVisits());
        $this->assertSame('blahblah', $entity->getChartData());
    }
}

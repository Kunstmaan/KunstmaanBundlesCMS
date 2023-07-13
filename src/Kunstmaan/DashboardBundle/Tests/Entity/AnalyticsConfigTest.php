<?php

namespace Kunstmaan\DashboardBundle\Tests\Entity;

use Kunstmaan\DashboardBundle\Entity\AnalyticsConfig;
use PHPUnit\Framework\TestCase;

class AnalyticsConfigTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $entity = new AnalyticsConfig();
        $entity->setId(666);
        $entity->setOverviews([]);
        $entity->setSegments([]);
        $entity->setName('Donald Trump');
        $entity->setToken('blahblah');
        $entity->setPropertyId('blahblah2');
        $entity->setAccountId('blahblah3');
        $entity->setProfileId('blahblah4');
        $entity->setLastUpdate(new \DateTime());
        $entity->setDisableGoals(true);

        $this->assertSame(666, $entity->getId());
        $this->assertIsArray($entity->getOverviews());
        $this->assertIsArray($entity->getSegments());
        $this->assertSame('Donald Trump', $entity->getName());
        $this->assertSame('blahblah', $entity->getToken());
        $this->assertSame('blahblah2', $entity->getPropertyId());
        $this->assertSame('blahblah3', $entity->getAccountId());
        $this->assertSame('blahblah4', $entity->getProfileId());
        $this->assertInstanceOf(\DateTime::class, $entity->getLastUpdate());
        $this->assertTrue($entity->getDisableGoals());
    }
}

<?php

namespace Kunstmaan\ConfigBundle\Tests\Entity;

use DateTime;
use Kunstmaan\DashboardBundle\Entity\AnalyticsConfig;
use PHPUnit_Framework_TestCase;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2012-09-04 at 16:54:04.
 */
class AnalyticsConfigTest extends PHPUnit_Framework_TestCase
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
        $entity->setLastUpdate(new DateTime());
        $entity->setDisableGoals(true);

        $this->assertEquals(666, $entity->getId());
        $this->assertTrue(is_array($entity->getOverviews()));
        $this->assertTrue(is_array($entity->getSegments()));
        $this->assertEquals('Donald Trump', $entity->getName());
        $this->assertEquals('blahblah', $entity->getToken());
        $this->assertEquals('blahblah2', $entity->getPropertyId());
        $this->assertEquals('blahblah3', $entity->getAccountId());
        $this->assertEquals('blahblah4', $entity->getProfileId());
        $this->assertInstanceOf(DateTime::class, $entity->getLastUpdate());
        $this->assertTrue($entity->getDisableGoals());
    }
}

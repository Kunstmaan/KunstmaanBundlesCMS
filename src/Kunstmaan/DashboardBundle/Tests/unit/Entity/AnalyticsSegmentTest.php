<?php

namespace Kunstmaan\ConfigBundle\Tests\Entity;

use Kunstmaan\DashboardBundle\Entity\AnalyticsSegment;
use PHPUnit\Framework\TestCase;

class AnalyticsSegmentTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $entity = new AnalyticsSegment();
        $entity->setName('Donald Trump');
        $entity->setQuery('query');
        $entity->setConfig(5);
        $entity->setoverviews([]);

        $this->assertEquals('Donald Trump', $entity->getName());
        $this->assertEquals('query', $entity->getQuery());
        $this->assertEquals(5, $entity->getConfig());
        $this->assertIsArray($entity->getoverviews());
    }
}

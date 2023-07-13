<?php

namespace Kunstmaan\DashboardBundle\Tests\Entity;

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

        $this->assertSame('Donald Trump', $entity->getName());
        $this->assertSame('query', $entity->getQuery());
        $this->assertSame(5, $entity->getConfig());
        $this->assertIsArray($entity->getoverviews());
    }
}

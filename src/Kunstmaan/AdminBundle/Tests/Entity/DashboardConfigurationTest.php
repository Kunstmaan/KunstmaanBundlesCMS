<?php

namespace Kunstmaan\AdminBundle\Tests\Entity;

use Kunstmaan\AdminBundle\Entity\DashboardConfiguration;
use PHPUnit\Framework\TestCase;

class DashboardConfigurationTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $entity = new DashboardConfiguration();
        $entity->setId(666);
        $entity->setTitle('Number of the beast');
        $entity->setContent('Iron Maiden');

        $this->assertEquals(666, $entity->getId());
        $this->assertEquals('Number of the beast', $entity->getTitle());
        $this->assertEquals('Iron Maiden', $entity->getContent());
    }
}

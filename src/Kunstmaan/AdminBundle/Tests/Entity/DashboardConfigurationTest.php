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

        $this->assertSame(666, $entity->getId());
        $this->assertSame('Number of the beast', $entity->getTitle());
        $this->assertSame('Iron Maiden', $entity->getContent());
    }
}

<?php

namespace Kunstmaan\SeoBundle\Tests\Entity;

use Kunstmaan\SeoBundle\Entity\Robots;
use Kunstmaan\SeoBundle\Form\RobotsType;
use PHPUnit\Framework\TestCase;

class RobotsTest extends TestCase
{
    /**
     * @var Robots
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new Robots();
    }

    public function testGettersSetters()
    {
        $this->object->setId(11);
        $this->object->setRobotsTxt('*');

        $this->assertSame(11, $this->object->getId());
        $this->assertSame('*', $this->object->getRobotsTxt());
        $this->assertSame(RobotsType::class, $this->object->getDefaultAdminType());
        $this->assertSame('Robots', $this->object->__toString());
    }
}

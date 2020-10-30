<?php

namespace Kunstmaan\SeoBundle\Tests\Entity;

use Kunstmaan\SeoBundle\Entity\Robots;
use Kunstmaan\SeoBundle\Form\RobotsType;
use PHPUnit\Framework\TestCase;

/**
 * Class RobotsTest
 */
class RobotsTest extends TestCase
{
    /**
     * @var Robots
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->object = new Robots();
    }

    public function testGettersSetters()
    {
        $this->object->setId(11);
        $this->object->setRobotsTxt('*');

        $this->assertEquals(11, $this->object->getId());
        $this->assertEquals('*', $this->object->getRobotsTxt());
        $this->assertEquals(RobotsType::class, $this->object->getDefaultAdminType());
        $this->assertEquals('Robots', $this->object->__toString());
    }
}

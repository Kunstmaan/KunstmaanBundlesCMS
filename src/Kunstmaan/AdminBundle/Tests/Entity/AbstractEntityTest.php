<?php

namespace Kunstmaan\AdminBundle\Tests\Entity;

use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use PHPUnit\Framework\TestCase;

class AbstractEntityTest extends TestCase
{
    /**
     * @var AbstractEntity
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = $this->getMockForAbstractClass('Kunstmaan\AdminBundle\Entity\AbstractEntity');
    }

    public function testGetSetId()
    {
        $this->object->setId(5);
        $this->assertEquals(5, $this->object->getId());
    }

    public function test__toString()
    {
        $this->object->setId(8);
        $this->assertEquals('8', $this->object->__toString());
    }
}

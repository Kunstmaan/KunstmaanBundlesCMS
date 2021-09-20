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

    protected function setUp(): void
    {
        $this->object = $this->getMockForAbstractClass('Kunstmaan\AdminBundle\Entity\AbstractEntity');
    }

    public function testGetSetId()
    {
        $this->object->setId(5);
        $this->assertEquals(5, $this->object->getId());
    }

    public function testToString()
    {
        $this->object->setId(8);
        $this->assertEquals('8', $this->object->__toString());
    }
}

<?php

namespace Kunstmaan\AdminBundle\Tests\Entity;

use Kunstmaan\AdminBundle\Entity\Role;
use PHPUnit\Framework\TestCase;

class RoleTest extends TestCase
{
    /**
     * @var Role
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new Role('ROLE_TEST');
    }

    public function testConstruct()
    {
        $this->assertEquals('ROLE_TEST', $this->object->getRole());
    }

    public function testGetSetRole()
    {
        $this->object->setRole('ROLE_CUSTOM');
        $this->assertEquals('ROLE_CUSTOM', $this->object->getRole());
    }

    public function testToString()
    {
        $this->assertEquals('ROLE_TEST', $this->object->__toString());
    }

    public function testGetId()
    {
        $this->assertEquals(null, $this->object->getId());
    }
}

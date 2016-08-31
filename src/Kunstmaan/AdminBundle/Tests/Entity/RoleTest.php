<?php

namespace Kunstmaan\AdminBundle\Tests\Entity;

use Kunstmaan\AdminBundle\Entity\Role;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2012-09-04 at 16:53:56.
 */
class RoleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Role
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Role('ROLE_TEST');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Kunstmaan\AdminBundle\Entity\Role::__construct
     */
    public function test__construct()
    {
        $this->assertEquals('ROLE_TEST', $this->object->getRole());
    }

    /**
     * @covers Kunstmaan\AdminBundle\Entity\Role::getRole
     * @covers Kunstmaan\AdminBundle\Entity\Role::setRole
     */
    public function testGetSetRole()
    {
        $this->object->setRole('ROLE_CUSTOM');
        $this->assertEquals('ROLE_CUSTOM', $this->object->getRole());
    }

    /**
     * @covers Kunstmaan\AdminBundle\Entity\Role::__toString
     */
    public function test__toString()
    {
        $this->assertEquals('ROLE_TEST', $this->object->__toString());
    }

    /**
     * @covers Kunstmaan\AdminBundle\Entity\Role::getId
     */
    public function testGetId()
    {
        $this->assertEquals(null, $this->object->getId());
    }
}

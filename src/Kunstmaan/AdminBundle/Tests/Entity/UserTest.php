<?php

namespace Kunstmaan\AdminBundle\Tests\Entity;

use Kunstmaan\AdminBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\GroupInterface;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2012-09-04 at 16:54:04.
 */
class UserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var User
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new User();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Kunstmaan\AdminBundle\Entity\User::__construct
     */
    public function test__construct()
    {
        $object = new User();
        $object->setId(1);
        $this->assertEquals(1, $object->getId());
    }

    /**
     * @covers Kunstmaan\AdminBundle\Entity\User::getId
     * @covers Kunstmaan\AdminBundle\Entity\User::setId
     */
    public function testGetSetId()
    {
        $this->object->setId(3);
        $this->assertEquals(3, $this->object->getId());
    }

    /**
     * @covers Kunstmaan\AdminBundle\Entity\User::getGroupIds
     */
    public function testGetGroupIds()
    {
        $group1 = $this->getMock('FOS\UserBundle\Model\GroupInterface');
        $group1
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));

        $group2 = $this->getMock('FOS\UserBundle\Model\GroupInterface');
        $group2
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(2));

        /* @var $group1 GroupInterface */
        $this->object->addGroup($group1);
        /* @var $group2 GroupInterface */
        $this->object->addGroup($group2);

        $this->assertEquals(array(1, 2), $this->object->getGroupIds());
    }

    /**
     * @covers Kunstmaan\AdminBundle\Entity\User::getGroups
     */
    public function testGetGroups()
    {
        /* @var $group1 GroupInterface */
        $group1 = $this->getMock('FOS\UserBundle\Model\GroupInterface');
        /* @var $group2 GroupInterface */
        $group2 = $this->getMock('FOS\UserBundle\Model\GroupInterface');
        $this->object->addGroup($group1);
        $this->object->addGroup($group2);

        $collection = new ArrayCollection();
        $collection->add($group1);
        $collection->add($group2);

        $this->assertEquals($collection, $this->object->getGroups());
    }

    /**
     * @covers Kunstmaan\AdminBundle\Entity\User::hasRole
     */
    public function testHasRole()
    {
        $this->object->addRole('ROLE_CUSTOM');
        $this->assertTrue($this->object->hasRole('ROLE_CUSTOM'));

        $this->object->removeRole('ROLE_CUSTOM');
        $this->assertFalse($this->object->hasRole('ROLE_CUSTOM'));
    }
}

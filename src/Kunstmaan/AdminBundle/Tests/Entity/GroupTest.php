<?php

namespace Kunstmaan\AdminBundle\Tests\Entity;

use Kunstmaan\AdminBundle\Entity\Group;
use Kunstmaan\AdminBundle\Entity\Role;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class GroupTest extends TestCase
{
    /**
     * @var Group
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new Group('group');
    }

    public function testGetId()
    {
        $this->assertEquals(null, $this->object->getId());
    }

    public function testToString()
    {
        $this->assertEquals('group', $this->object->__toString());
    }

    public function testGetRoles()
    {
        /* @var $role Role */
        $role = $this->getRole();
        $this->object->addRole($role);

        $this->assertEquals(['role1'], $this->object->getRoles());
    }

    public function testGetRolesCollection()
    {
        /* @var $role Role */
        $role = $this->getRole();
        $this->object->addRole($role);

        $collection = new \Doctrine\Common\Collections\ArrayCollection();
        $collection->add($role);

        $this->assertEquals($collection, $this->object->getRolesCollection());
    }

    public function testGetRole()
    {
        /* @var $role Role */
        $role = $this->getRole();
        $this->object->addRole($role);

        $result = $this->object->getRole('role1');
        $this->assertEquals($role, $result);

        $result = $this->object->getRole('role2');
        $this->assertEquals(null, $result);
    }

    public function testHasRole()
    {
        /* @var $role Role */
        $role = $this->getRole();
        $this->object->addRole($role);

        $this->assertTrue($this->object->hasRole('role1'));
        $this->assertFalse($this->object->hasRole('role2'));
    }

    public function testRemoveRole()
    {
        /* @var $role Role */
        $role = $this->getRole();
        $this->object->addRole($role);
        $this->assertTrue($this->object->hasRole('role1'));

        $this->object->removeRole('role1');
        $this->assertFalse($this->object->hasRole('role1'));
    }

    public function testAddRoleWithInvalidParameter()
    {
        $this->expectException(\InvalidArgumentException::class);
        /* @var $role Role */
        $role = new \StdClass();
        $this->object->addRole($role);
    }

    public function testSetRoles()
    {
        $role1 = $this->getRole('role1');
        $role2 = $this->getRole('role2');
        $role3 = $this->getRole('role3');
        $roles = [$role1, $role2, $role3];
        $this->object->setRoles($roles);

        $this->assertCount(3, $this->object->getRoles());
    }

    public function testSetRolesCollection()
    {
        $role1 = $this->getRole('role1');
        $role2 = $this->getRole('role2');
        $role3 = $this->getRole('role3');

        $roles = new \Doctrine\Common\Collections\ArrayCollection();
        $roles->add($role1);
        $roles->add($role2);
        $roles->add($role3);

        $this->object->setRolesCollection($roles);
        $this->assertEquals(3, $this->object->getRolesCollection()->count());
    }

    public function testConstructorAndGetSetName()
    {
        $object = new Group('testgroup');
        $this->assertEquals('testgroup', $object->getName());

        $object->setName('group2');
        $this->assertEquals('group2', $object->getName());
    }

    public function testValidateGroupWithoutRole()
    {
        $group = new Group('test');

        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator();

        $violations = $validator->validate($group);

        $this->assertCount(1, $violations);
    }

    public function testValidateGroupWithRole()
    {
        $group = new Group('test');
        $group->addRole(new Role('role'));

        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator();

        $violations = $validator->validate($group);

        $this->assertCount(0, $violations);
    }

    /**
     * @param string $name
     *
     * @return \Kunstmaan\AdminBundle\Entity\Role
     */
    protected function getRole($name = 'role1')
    {
        $role = $this->getMockBuilder('Kunstmaan\AdminBundle\Entity\Role')
            ->disableOriginalConstructor()
            ->getMock();
        $role->expects($this->any())
            ->method('getRole')
            ->will($this->returnValue($name));

        return $role;
    }
}

<?php

namespace Kunstmaan\AdminBundle\Tests\Entity;

use Kunstmaan\AdminBundle\Form\UserType;
use Doctrine\Common\Collections\ArrayCollection;
use Kunstmaan\AdminBundle\Entity\GroupInterface;
use Kunstmaan\AdminBundle\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class UserTest extends TestCase
{
    /**
     * @var User
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new User();
    }

    public function testConstruct()
    {
        $object = new User();
        $object->setId(1);
        $this->assertSame(1, $object->getId());
    }

    public function testGetSetId()
    {
        $this->object->setId(3);
        $this->assertSame(3, $this->object->getId());
    }

    public function testGetGroupIds()
    {
        $group1 = $this->createMock(GroupInterface::class);
        $group1
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));

        $group2 = $this->createMock(GroupInterface::class);
        $group2
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(2));

        /* @var $group1 GroupInterface */
        $this->object->addGroup($group1);
        /* @var $group2 GroupInterface */
        $this->object->addGroup($group2);

        $this->assertSame([1, 2], $this->object->getGroupIds());
    }

    public function testGetGroups()
    {
        /* @var $group1 GroupInterface */
        $group1 = $this->createMock(GroupInterface::class);
        /* @var $group2 GroupInterface */
        $group2 = $this->createMock(GroupInterface::class);
        $this->object->addGroup($group1);
        $this->object->addGroup($group2);

        $collection = new ArrayCollection();
        $collection->add($group1);
        $collection->add($group2);

        $this->assertEquals($collection, $this->object->getGroups());
    }

    public function testHasRole()
    {
        $this->object->addRole('ROLE_CUSTOM');
        $this->assertTrue($this->object->hasRole('ROLE_CUSTOM'));

        $this->object->removeRole('ROLE_CUSTOM');
        $this->assertFalse($this->object->hasRole('ROLE_CUSTOM'));
    }

    public function testGettersAndSetters()
    {
        $user = $this->object;
        $user->setAdminLocale('en');
        $user->setPasswordChanged(true);
        $user->setGoogleId('g0oGl3');
        $user->setEnabled(true);

        $this->assertSame('en', $user->getAdminLocale());
        $this->assertSame('g0oGl3', $user->getGoogleId());
        $this->assertTrue($user->isPasswordChanged());
        $this->assertTrue($user->isAccountNonLocked());
        $this->assertSame(UserType::class, $user->getFormTypeClass());
    }

    public function testLoadValidatorMetadata()
    {
        $meta = new ClassMetadata(User::class);
        User::loadValidatorMetadata($meta);
        $this->assertSame(User::class, $meta->getClassName());
        $this->assertSame('User', $meta->getDefaultGroup());
        $props = $meta->getConstrainedProperties();
        $this->assertCount(3, $props);
        $this->assertSame('username', $props[0]);
        $this->assertSame('plainPassword', $props[1]);
        $this->assertSame('email', $props[2]);
    }
}

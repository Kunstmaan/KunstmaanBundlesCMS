<?php

namespace Kunstmaan\AdminBundle\Tests\Entity;

use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\AdminBundle\Entity\AclChangeset;
use PHPUnit\Framework\TestCase;

class AclChangesetTest extends TestCase
{
    /**
     * @var AclChangeset
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new AclChangeset();
    }

    public function testConstruct()
    {
        $object = new AclChangeset();
        $this->assertEquals(AclChangeset::STATUS_NEW, $object->getStatus());
    }

    public function testSetAndGetChangeset()
    {
        $changeset = ['ROLE_ADMIN' => ['ADD' => ['VIEW', 'EDIT'], 'DEL' => 'PUBLISH']];
        $this->object->setChangeset($changeset);

        $this->assertEquals($changeset, $this->object->getChangeset());
    }

    public function testSetAndGetCreated()
    {
        $currentDate = new \DateTime('now');
        $this->object->setCreated($currentDate);

        $this->assertEquals($currentDate, $this->object->getCreated());
    }

    public function testSetAndGetLastModified()
    {
        $currentDate = new \DateTime('now');
        $this->object->setLastModified($currentDate);

        $this->assertEquals($currentDate, $this->object->getLastModified());
    }

    public function testSetAndGetRef()
    {
        $entity = $this->createMock(AbstractEntity::class);
        $entity->method('getId')->willReturn(1);
        $entity->method('setId')->willReturn(null);
        $entity->method('__toString')->willReturn('1');

        $this->object->setRef($entity);
        $this->assertEquals(1, $this->object->getRefId());
        $this->assertEquals(\get_class($entity), $this->object->getRefEntityName());
    }

    public function testSetAndGetStatus()
    {
        $yesterday = new \DateTime('yesterday');
        $this->object->setLastModified($yesterday);

        $this->assertEquals(AclChangeset::STATUS_NEW, $this->object->getStatus());

        $this->object->setStatus(AclChangeset::STATUS_RUNNING);
        $this->assertNotEquals(AclChangeset::STATUS_NEW, $this->object->getStatus());
        $this->assertEquals(AclChangeset::STATUS_RUNNING, $this->object->getStatus());
        $this->assertNotEquals($yesterday, $this->object->getLastModified());
    }

    public function testSetAndGetPid()
    {
        $this->object->setPid(10);
        $this->assertEquals(10, $this->object->getPid());
    }

    public function testSetAndGetUser()
    {
        $user = new \Kunstmaan\AdminBundle\Entity\User();
        $this->object->setUser($user);

        $this->assertEquals($user, $this->object->getUser());
    }
}

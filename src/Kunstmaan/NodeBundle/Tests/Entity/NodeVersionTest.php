<?php

namespace Kunstmaan\NodeBundle\Tests\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use PHPUnit\Framework\TestCase;

class NodeVersionTest extends TestCase
{
    /**
     * @var NodeVersion
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new NodeVersion();
    }

    public function testSetGetNodeTranslation()
    {
        $nodeTrans = new NodeTranslation();
        $this->object->setNodeTranslation($nodeTrans);
        $this->assertEquals($nodeTrans, $this->object->getNodeTranslation());
    }

    public function testSetGetType()
    {
        $this->object->setType(NodeVersion::DRAFT_VERSION);
        $this->assertEquals(NodeVersion::DRAFT_VERSION, $this->object->getType());
    }

    public function testSetGetOwner()
    {
        $this->object->setOwner('owner');
        $this->assertEquals('owner', $this->object->getOwner());
    }

    public function testSetGetCreated()
    {
        $created = new \DateTime();
        $this->object->setCreated($created);
        $this->assertEquals($created, $this->object->getCreated());
    }

    public function testSetGetUpdated()
    {
        $updated = new \DateTime();
        $this->object->setUpdated($updated);
        $this->assertEquals($updated, $this->object->getUpdated());
    }

    public function testGetSetRef()
    {
        $entity = $this->createMock(HasNodeInterface::class);
        $entity->method('getId')->willReturn(1);

        $em = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repo = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repo->expects($this->any())
            ->method('find')
            ->willReturn($entity);

        $em->expects($this->any())
            ->method('getRepository')
            ->willReturn($repo);

        $this->object->setRef($entity);
        $this->assertEquals(1, $this->object->getRefId());
        $this->assertEquals(\get_class($entity), $this->object->getRefEntityName());
        $this->assertInstanceOf(\get_class($entity), $this->object->getRef($em));
    }

    public function testGetDefaultAdminType()
    {
        $this->assertNull($this->object->getDefaultAdminType());
    }

    public function testGetSetOrigin()
    {
        $entity = new NodeVersion();
        $this->object->setOrigin($entity);
        $this->assertInstanceOf(NodeVersion::class, $this->object->getOrigin());
    }

    public function testIsPublic()
    {
        $this->object->setType(NodeVersion::PUBLIC_VERSION);
        $this->assertTrue($this->object->isPublic());
    }
}

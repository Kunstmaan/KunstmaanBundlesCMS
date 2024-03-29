<?php

namespace Kunstmaan\NodeBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use PHPUnit\Framework\TestCase;

class NodeTranslationTest extends TestCase
{
    /**
     * @var NodeTranslation
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new NodeTranslation();
    }

    public function testSetGetNode()
    {
        $node = new Node();
        $this->object->setNode($node);
        $this->assertEquals($node, $this->object->getNode());
    }

    public function testSetGetLang()
    {
        $this->object->setLang('nl');
        $this->assertEquals('nl', $this->object->getLang());
    }

    public function testSetIsOnline()
    {
        $this->object->setOnline(true);
        $this->assertTrue($this->object->isOnline());
    }

    public function testSetGetTitle()
    {
        $this->object->setTitle('A node translation title');
        $this->assertEquals('A node translation title', $this->object->getTitle());
    }

    public function testSetGetSlug()
    {
        $this->object->setSlug('a-node-translation-slug');
        $this->assertEquals('a-node-translation-slug', $this->object->getSlug());
    }

    public function testGetFullSlug()
    {
        $parentNode = $this->getNodeWithTranslation('nl', 'Parent node title', 'parent-node-slug');

        $childNode = new Node();
        $childNode->setParent($parentNode);
        $childNodeTrans = new NodeTranslation();
        $childNodeTrans->setLang('nl')
            ->setSlug('child-node-slug');
        $childNode->addNodeTranslation($childNodeTrans);

        $this->assertEquals('parent-node-slug/child-node-slug', $childNodeTrans->getFullSlug());
    }

    public function testGetFullSlugWithEmptySlug()
    {
        $childNode = new Node();
        $childNodeTrans = new NodeTranslation();
        $childNodeTrans->setLang('nl')
            ->setSlug('');
        $childNode->addNodeTranslation($childNodeTrans);

        $this->assertNull($childNodeTrans->getFullSlug());
    }

    public function testSetGetPublicNodeVersion()
    {
        $nodeVersion = new NodeVersion();
        $this->object->setPublicNodeVersion($nodeVersion);
        $this->assertEquals($nodeVersion, $this->object->getPublicNodeVersion());
    }

    public function testSetGetNodeVersions()
    {
        $nodeVersions = new ArrayCollection();
        $nodeVersion1 = new NodeVersion();
        $nodeVersion1->setType('public');
        $nodeVersions->add($nodeVersion1);
        $nodeVersion2 = new NodeVersion();
        $nodeVersion2->setType('draft');
        $nodeVersions->add($nodeVersion2);
        $this->object->setNodeVersions($nodeVersions);
        $this->assertEquals(2, $this->object->getNodeVersions()->count());
    }

    public function testAddGetNodeVersion()
    {
        $nodeVersion1 = new NodeVersion();
        $nodeVersion1->setType('public');
        $this->object->addNodeVersion($nodeVersion1);
        $nodeVersion2 = new NodeVersion();
        $nodeVersion2->setType('draft');
        $this->object->addNodeVersion($nodeVersion2);
        $this->assertEquals($nodeVersion1, $this->object->getNodeVersion('public'));
        $this->assertEquals($nodeVersion2, $this->object->getNodeVersion('draft'));
    }

    public function testGetNonExistentNodeVersion()
    {
        $this->assertNull($this->object->getNodeVersion('draft'));
    }

    public function testGetDefaultAdminType()
    {
        $this->assertEquals('Kunstmaan\NodeBundle\Form\NodeTranslationAdminType', $this->object->getDefaultAdminType());
    }

    public function testSetGetUrl()
    {
        $this->object->setUrl('parent/child-url');
        $this->assertEquals('parent/child-url', $this->object->getUrl());
    }

    public function testSetGetWeight()
    {
        $this->object->setWeight(10);
        $this->assertEquals(10, $this->object->getWeight());
    }

    private function getNodeWithTranslation($lang, $title, $slug, $nodeId = null)
    {
        $node = new Node();
        if (!\is_null($nodeId)) {
            $node->setId($nodeId);
        }
        $nodeTranslation = new NodeTranslation();
        $nodeTranslation->setLang($lang)
            ->setTitle($title)
            ->setSlug($slug);
        $node->addNodeTranslation($nodeTranslation);

        return $node;
    }

    public function testGetDraftNodeTranslation()
    {
        $nodeVersion = new NodeVersion();
        $nodeVersion->setType(NodeVersion::DRAFT_VERSION);
        $collection = new ArrayCollection([$nodeVersion]);
        $this->object->setNodeVersions($collection);

        $this->assertInstanceOf(NodeVersion::class, $this->object->getDraftNodeVersion());
    }

    public function testGetSetCreatedUpdated()
    {
        $date = new \DateTime('2014-09-18');
        $tomorrow = clone $date;
        $tomorrow->modify('+1 day');
        $this->object->setCreated($date);
        $this->object->setUpdated($tomorrow);
        $this->assertInstanceOf(\DateTime::class, $this->object->getCreated());
        $this->assertInstanceOf(\DateTime::class, $this->object->getUpdated());
        $this->assertEquals('2014-09-18', $this->object->getCreated()->format('Y-m-d'));
        $this->assertEquals('2014-09-19', $this->object->getUpdated()->format('Y-m-d'));
    }

    public function testGetSetRef()
    {
        $entity = new NodeVersion();
        $entity->setType(NodeVersion::PUBLIC_VERSION);

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

        $this->assertNull($this->object->getRef($em));

        $this->object->setPublicNodeVersion($entity);
        $this->assertInstanceOf(NodeVersion::class, $this->object->getRef($em));
    }
}

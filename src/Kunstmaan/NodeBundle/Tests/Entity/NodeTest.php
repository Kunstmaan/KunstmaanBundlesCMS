<?php

namespace Kunstmaan\NodeBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use PHPUnit\Framework\TestCase;

class NodeTest extends TestCase
{
    /**
     * @var Node
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new Node();
    }

    public function testIsGetSetHiddenFromNav()
    {
        $this->assertFalse($this->object->getHiddenFromNav());
        $this->object->setHiddenFromNav(true);
        $this->assertTrue($this->object->isHiddenFromNav());
        $this->assertTrue($this->object->getHiddenFromNav());
    }

    public function testGetSetChildren()
    {
        $children = new ArrayCollection();
        $child = new Node();
        $children->add($child);
        $this->object->setChildren($children);

        $this->assertEquals(1, $this->object->getChildren()->count());
        $this->assertEquals($children, $this->object->getChildren());
        $this->assertTrue($this->object->getChildren()->contains($child));
    }

    public function testGetSetChildrenWithDeletedChildren()
    {
        $children = new ArrayCollection();
        $child = new Node();
        $deletedChild = new Node();
        $deletedChild->setDeleted(true);
        $children->add($child);
        $children->add($deletedChild);
        $this->object->setChildren($children);

        $this->assertEquals(1, $this->object->getChildren()->count());
        $this->assertTrue($this->object->getChildren()->contains($child));
        $this->assertFalse($this->object->getChildren()->contains($deletedChild));
    }

    public function testAddNode()
    {
        $child = new Node();
        $this->object->addNode($child);
        $this->assertEquals($this->object, $child->getParent());
        $this->assertEquals(1, $this->object->getChildren()->count());
    }

    public function testGetSetNodeTranslations()
    {
        $translations = new ArrayCollection();
        $translation = new NodeTranslation();
        $translations->add($translation);
        $this->object->setNodeTranslations($translations);

        $this->assertEquals(1, $this->object->getNodeTranslations(true)->count());
        $this->assertEquals($translations, $this->object->getNodeTranslations(true));
        $this->assertTrue($this->object->getNodeTranslations(true)->contains($translation));
    }

    public function testGetNodeTranslationsWithOfflineNodes()
    {
        $translation1 = new NodeTranslation();
        $translation1->setOnline(true);
        $this->object->addNodeTranslation($translation1);

        $translation2 = new NodeTranslation();
        $translation2->setOnline(false);
        $this->object->addNodeTranslation($translation2);

        $this->assertEquals(2, $this->object->getNodeTranslations(true)->count());
        $this->assertEquals(1, $this->object->getNodeTranslations()->count());
    }

    public function testGetNodeTranslation()
    {
        $translation1 = new NodeTranslation();
        $translation1->setLang('nl');
        $translation1->setOnline(true);
        $this->object->addNodeTranslation($translation1);

        $translation2 = new NodeTranslation();
        $translation2->setLang('fr');
        $translation2->setOnline(true);
        $this->object->addNodeTranslation($translation2);

        $this->assertEquals($translation1, $this->object->getNodeTranslation('nl'));
        $this->assertEquals($translation2, $this->object->getNodeTranslation('fr'));
        $this->assertNotEquals($translation1, $this->object->getNodeTranslation('fr'));
        $this->assertNotEquals($translation2, $this->object->getNodeTranslation('nl'));
        $this->assertNull($this->object->getNodeTranslation('en'));
    }

    public function testGetParents()
    {
        $child = new Node();
        $grandChild = new Node();
        $child->addNode($grandChild);
        $this->object->addNode($child);
        $parents = $grandChild->getParents();

        $this->assertCount(2, $parents);
        $this->assertEquals($child, $parents[1]);
        $this->assertEquals($this->object, $parents[0]);
    }

    public function testIsSetDeleted()
    {
        $this->assertFalse($this->object->isDeleted());
        $this->object->setDeleted(true);
        $this->assertTrue($this->object->isDeleted());
    }

    public function testSetRefAndGetRefEntityName()
    {
        $entity = $this->createMock(HasNodeInterface::class);
        $this->object->setRef($entity);
        $this->assertEquals(\get_class($entity), $this->object->getRefEntityName());
    }

    public function testSetInternalName()
    {
        $this->object->setInternalName('AnInternalName');
        $this->assertEquals('AnInternalName', $this->object->getInternalName());
    }

    public function testGetDefaultAdminType()
    {
        $this->assertEquals('Kunstmaan\NodeBundle\Form\NodeAdminType', $this->object->getDefaultAdminType());
    }

    public function testToString()
    {
        $entity = $this->createMock(HasNodeInterface::class);
        $this->object->setId(1);
        $this->object->setRef($entity);

        $this->assertEquals('node 1, refEntityName: ' . \get_class($entity), $this->object->__toString());
    }

    public function testGetSetLeftRightLevel()
    {
        $mirror = new \ReflectionClass(Node::class);
        $property = $mirror->getProperty('lft');
        $property->setAccessible(true);
        $property->setValue($this->object, 11);
        $property = $mirror->getProperty('rgt');
        $property->setAccessible(true);
        $property->setValue($this->object, 12);
        $property = $mirror->getProperty('lvl');
        $property->setAccessible(true);
        $property->setValue($this->object, 13);

        $this->assertEquals(11, $this->object->getLeft());
        $this->assertEquals(12, $this->object->getRight());
        $this->assertEquals(13, $this->object->getLevel());
    }
}

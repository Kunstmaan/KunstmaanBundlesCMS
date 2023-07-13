<?php

namespace Kunstmaan\MediaBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\Entity\Media;
use PHPUnit\Framework\TestCase;

class FolderTest extends TestCase
{
    /**
     * @var Folder
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new Folder();
    }

    public function testGetSetName()
    {
        $this->object->setName('name');
        $this->assertSame('name', $this->object->getName());
        $this->assertSame('name', $this->object->__toString());
    }

    public function testGetSetRel()
    {
        $this->object->setRel('rel');
        $this->assertSame('rel', $this->object->getRel());
    }

    public function testGetSetCreatedAt()
    {
        $date = new \DateTime();
        $this->object->setCreatedAt($date);
        $this->assertEquals($date, $this->object->getCreatedAt());
    }

    public function testGetSetUpdatedAt()
    {
        $this->object->preUpdate();
        $date = new \DateTime();
        $this->object->setUpdatedAt($date);
        $this->assertEquals($date, $this->object->getUpdatedAt());
    }

    public function testGetParent()
    {
        $parent = new Folder();
        $parent->setId(45);
        $this->object->setParent($parent);
        $this->assertSame(45, $this->object->getParent()->getId());
    }

    public function testGetSetDeleted()
    {
        $this->assertFalse($this->object->isDeleted());

        $this->object->setDeleted(true);
        $this->assertTrue($this->object->isDeleted());
    }

    public function testGetSetTranslatableLocale()
    {
        $this->object->setTranslatableLocale('nl');
        $this->assertSame('nl', $this->object->getTranslatableLocale());
    }

    public function testGetParents()
    {
        $root = new Folder();
        $root->setId(1);

        $subFolder = new Folder();
        $subFolder->setId(2);
        $subFolder->setParent($root);

        $subSubFolder = new Folder();
        $subSubFolder->setId(3);
        $subSubFolder->setParent($subFolder);

        $parents = [$root, $subFolder];
        $this->assertEquals($parents, $subSubFolder->getParents());
    }

    public function testAddChild()
    {
        $this->assertCount(0, $this->object->getChildren());

        $subFolder = new Folder();
        $subFolder->setId(2);
        $this->object->addChild($subFolder);

        $this->assertCount(1, $this->object->getChildren());
        $this->assertEquals($this->object, $subFolder->getParent());
    }

    public function testAddMedia()
    {
        $this->assertCount(0, $this->object->getMedia());

        $media = new Media();
        $this->object->addMedia($media);

        $this->assertCount(1, $this->object->getMedia());
    }

    public function testGetMedia()
    {
        $media = new Media();
        $this->object->addMedia($media);

        $deletedMedia = new Media();
        $deletedMedia->setDeleted(true);
        $this->object->addMedia($deletedMedia);

        $this->assertCount(1, $this->object->getMedia());
        $this->assertCount(1, $this->object->getMedia(false));
        $this->assertCount(2, $this->object->getMedia(true));

        $folderMedia = $this->object->getMedia(false);
        $this->assertContains($media, $folderMedia);
        $this->assertNotContains($deletedMedia, $folderMedia);

        $folderMedia = $this->object->getMedia(true);
        $this->assertContains($media, $folderMedia);
        $this->assertContains($deletedMedia, $folderMedia);
    }

    public function testGetSetChildren()
    {
        $child = new Folder();

        $deletedChild = new Folder();
        $deletedChild->setDeleted(true);

        $children = new ArrayCollection();
        $children->add($child);
        $children->add($deletedChild);

        $this->object->setChildren($children);

        $this->assertCount(1, $this->object->getChildren());
        $this->assertCount(1, $this->object->getChildren(false));
        $this->assertCount(2, $this->object->getChildren(true));

        $children = $this->object->getChildren(false);
        $this->assertContains($child, $children);
        $this->assertNotContains($deletedChild, $children);

        $children = $this->object->getChildren(true);
        $this->assertContains($child, $children);
        $this->assertContains($deletedChild, $children);
    }

    public function testHasActive()
    {
        $root = new Folder();
        $root->setId(1);

        $subFolder = new Folder();
        $subFolder->setId(2);
        $root->addChild($subFolder);

        $subFolder2 = new Folder();
        $subFolder2->setId(4);
        $root->addChild($subFolder2);

        $subSubFolder = new Folder();
        $subSubFolder->setId(3);
        $subFolder->addChild($subSubFolder);

        $this->assertTrue($root->hasActive(2));
        $this->assertTrue($root->hasActive(4));
        $this->assertTrue($subFolder->hasActive(3));
        $this->assertFalse($subFolder->hasActive(4));
    }

    public function testGetSetInternalName()
    {
        $this->object->setInternalName('internal_name');
        $this->assertSame('internal_name', $this->object->getInternalName());
    }

    public function testGetSetLeft()
    {
        $this->assertSame(0, $this->object->getLeft());
        $this->object->setLeft(1);
        $this->assertSame(1, $this->object->getLeft());
    }

    public function testGetSetRight()
    {
        $this->assertSame(0, $this->object->getRight());
        $this->object->setRight(2);
        $this->assertSame(2, $this->object->getRight());
    }

    public function testGetSetLevel()
    {
        $this->assertSame(0, $this->object->getLevel());
        $this->object->setLevel(1);
        $this->assertSame(1, $this->object->getLevel());
    }

    public function testGetOptionLabel()
    {
        $this->object
            ->setName('Test')
            ->setLevel(2);

        $this->assertSame('-- Test', $this->object->getOptionLabel());
    }
}

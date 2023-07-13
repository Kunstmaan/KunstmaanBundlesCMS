<?php

namespace Kunstmaan\TaggingBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Kunstmaan\TaggingBundle\Entity\Taggable;
use Kunstmaan\TaggingBundle\Entity\TaggableTrait;
use PHPUnit\Framework\TestCase;

class Random implements Taggable
{
    use TaggableTrait;

    /**
     * @Id
     *
     * @GeneratedValue
     *
     * @Column(type="integer")
     */
    public $id;

    /**
     * @Column(name="title", type="string", length=50)
     */
    public $title;

    public function getId()
    {
        return 5;
    }
}

class TaggableTraitTest extends TestCase
{
    public function testGetters()
    {
        $object = new Random();

        $object->setTags(new ArrayCollection());
        $object->setTagLoader(fn() => new ArrayCollection());

        $this->assertInstanceOf(ArrayCollection::class, $object->getTags());
        $this->assertSame(Random::class, $object->getTaggableType());
        $this->assertSame(5, $object->getTaggableId());
    }

    public function testTagsByClosureLoader()
    {
        $object = new Random();
        $object->setTagLoader(fn() => new ArrayCollection());
        $this->assertInstanceOf(ArrayCollection::class, $object->getTags());
    }
}

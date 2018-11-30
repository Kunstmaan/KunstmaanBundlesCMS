<?php

namespace Kunstmaan\TaggingBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Kunstmaan\TaggingBundle\Entity\Taggable;
use Kunstmaan\TaggingBundle\Entity\TaggableTrait;
use PHPUnit_Framework_TestCase;

class Random implements Taggable
{
    use TaggableTrait;

    /**
     * @Id
     * @GeneratedValue
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

/**
 * Class TaggableTraitTest
 */
class TaggableTraitTest extends PHPUnit_Framework_TestCase
{
    public function testGetters()
    {
        $object = new Random();

        $object->setTags(new ArrayCollection());
        $object->setTagLoader(function () {
            return new ArrayCollection();
        });

        $this->assertInstanceOf(ArrayCollection::class, $object->getTags());
        $this->assertEquals(Random::class, $object->getTaggableType());
        $this->assertEquals(5, $object->getTaggableId());
    }

    public function testTagsByClosureLoader()
    {
        $object = new Random();
        $object->setTagLoader(function () {
            return new ArrayCollection();
        });
        $this->assertInstanceOf(ArrayCollection::class, $object->getTags());
    }
}

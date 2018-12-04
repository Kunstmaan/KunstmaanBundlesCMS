<?php

namespace Kunstmaan\TaggingBundle\Tests\Entity;

use DateTime;
use Kunstmaan\TaggingBundle\Entity\Tag;
use Kunstmaan\TaggingBundle\Entity\Tagging;
use PHPUnit_Framework_TestCase;

/**
 * Class TagTest
 */
class TaggingTest extends PHPUnit_Framework_TestCase
{
    public function testGetters()
    {
        $object = new Tagging();
        $today = new DateTime();

        $object->setId(666);
        $object->setCreatedAt($today);
        $object->setUpdatedAt($today);
        $object->setResourceType('something');
        $object->setResourceId(667);
        $object->setTag(new Tag());

        $this->assertEquals(666, $object->getId());
        $this->assertEquals(667, $object->getResourceId());
        $this->assertEquals('something', $object->getResourceType());
        $this->assertInstanceOf(DateTime::class, $object->getCreatedAt());
        $this->assertInstanceOf(DateTime::class, $object->getUpdatedAt());
        $this->assertInstanceOf(Tag::class, $object->getTag());
    }
}

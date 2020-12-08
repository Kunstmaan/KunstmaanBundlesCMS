<?php

namespace Kunstmaan\TaggingBundle\Tests\Entity;

use Kunstmaan\TaggingBundle\Entity\Tag;
use Kunstmaan\TaggingBundle\Form\TagAdminType;
use PHPUnit\Framework\TestCase;

class TagTest extends TestCase
{
    public function testGetters()
    {
        $object = new Tag();
        $today = new \DateTime();

        $object->setId(666);
        $object->setCreatedAt($today);
        $object->setUpdatedAt($today);
        $object->setName('Nigel Farage');
        $object->setTranslatableLocale('en');

        $this->assertEquals(666, $object->getId());
        $this->assertEquals('Nigel Farage', $object->getName());
        $this->assertEquals('en', $object->getTranslatableLocale());
        $this->assertInstanceOf(\DateTime::class, $object->getCreatedAt());
        $this->assertInstanceOf(\DateTime::class, $object->getUpdatedAt());
        $this->assertEquals(TagAdminType::class, $object->getDefaultAdminType());
        $this->assertEquals('Nigel Farage', $object->__toString());
    }
}

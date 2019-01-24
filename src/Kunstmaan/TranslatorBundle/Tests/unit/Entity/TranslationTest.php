<?php

namespace Kunstmaan\TranslationBundle\Tests\Entity;

use DateTime;
use Kunstmaan\TranslatorBundle\Entity\Translation;
use PHPUnit_Framework_TestCase;

/**
 * Class TranslationTest
 */
class TranslationTest extends PHPUnit_Framework_TestCase
{
    public function testGetSet()
    {
        $object = new Translation();

        $object->setId(666);
        $object->setCreatedAt(new DateTime());
        $object->setUpdatedAt(new DateTime());
        $object->setFile('rubber-chickens.pdf');
        $object->preUpdate();

        $this->assertEquals(666, $object->getId());
        $this->assertInstanceOf(DateTime::class, $object->getCreatedAt());
        $this->assertInstanceOf(DateTime::class, $object->getUpdatedAt());
        $this->assertEquals('rubber-chickens.pdf', $object->getFile());
        $this->assertEquals(Translation::FLAG_UPDATED, $object->getFlag());

        $object->setFlag(Translation::FLAG_NEW);
        $this->assertEquals(Translation::FLAG_NEW, $object->getFlag());
    }
}

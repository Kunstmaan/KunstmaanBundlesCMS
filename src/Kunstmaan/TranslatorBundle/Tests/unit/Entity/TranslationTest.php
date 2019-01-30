<?php

namespace Kunstmaan\TranslationBundle\Tests\Entity;

use Codeception\Test\Unit;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Kunstmaan\TranslatorBundle\Entity\Translation;
use Kunstmaan\TranslatorBundle\Model\TextWithLocale;

/**
 * Class TranslationTest
 */
class TranslationTest extends Unit
{
    const TEST_DATA_ID = 666;
    const TEST_DATA_FILE = 'messages.en.yml';
    const TEST_DATA_KEYWORD = 'hello.world';
    const TEST_DATA_TEXT = 'hello world';
    const TEST_DATA_DOMAIN = 'messages';

    protected $object;

    public function _before()
    {
        $this->object = new Translation();

        $this->object
            ->setId(self::TEST_DATA_ID)
            ->setCreatedAt(new DateTime())
            ->setUpdatedAt(new DateTime())
            ->setFile(self::TEST_DATA_FILE)
            ->setKeyword(self::TEST_DATA_KEYWORD)
            ->setDomain(self::TEST_DATA_DOMAIN)
            ->setText(self::TEST_DATA_TEXT)
        ;

        $this->object->preUpdate();
    }

    public function testGetSet()
    {
        $this->assertEquals(self::TEST_DATA_ID, $this->object->getId());
        $this->assertInstanceOf(DateTime::class, $this->object->getCreatedAt());
        $this->assertInstanceOf(DateTime::class, $this->object->getUpdatedAt());
        $this->assertEquals(self::TEST_DATA_FILE, $this->object->getFile());
        $this->assertEquals(Translation::FLAG_UPDATED, $this->object->getFlag());

        $this->object->setFlag(Translation::FLAG_NEW);
        $this->assertEquals(Translation::FLAG_NEW, $this->object->getFlag());
    }

    public function testGetTranslationModel()
    {
        $translationModel = $this->object->getTranslationModel(self::TEST_DATA_ID);
        $this->assertInstanceOf(\Kunstmaan\TranslatorBundle\Model\Translation::class, $translationModel);
        $this->assertEquals(self::TEST_DATA_KEYWORD, $translationModel->getKeyword());
        $this->assertEquals(self::TEST_DATA_DOMAIN, $translationModel->getDomain());

        $texts = $translationModel->getTexts();
        $this->assertInstanceOf(ArrayCollection::class, $texts);
        $this->assertInstanceOf(TextWithLocale::class, $texts->first());
    }

    public function testGetStatus()
    {
        $this->assertFalse($this->object->isDisabled());

        $this->object->setStatus(Translation::STATUS_DISABLED);
        $this->assertTrue($this->object->isDisabled());

        $this->object->setStatus(Translation::STATUS_DEPRECATED);
        $this->assertTrue($this->object->isDeprecated());
    }
}

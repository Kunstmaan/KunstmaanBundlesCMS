<?php

namespace Kunstmaan\TranslatorBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Kunstmaan\TranslatorBundle\Entity\Translation;
use Kunstmaan\TranslatorBundle\Model\TextWithLocale;
use PHPUnit\Framework\TestCase;

class TranslationTest extends TestCase
{
    public const TEST_DATA_ID = 666;
    public const TEST_DATA_FILE = 'messages.en.yml';
    public const TEST_DATA_KEYWORD = 'hello.world';
    public const TEST_DATA_TEXT = 'hello world';
    public const TEST_DATA_DOMAIN = 'messages';

    protected $object;

    public function setUp(): void
    {
        $this->object = new Translation();

        $this->object
            ->setId(self::TEST_DATA_ID)
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime())
            ->setFile(self::TEST_DATA_FILE)
            ->setKeyword(self::TEST_DATA_KEYWORD)
            ->setDomain(self::TEST_DATA_DOMAIN)
            ->setText(self::TEST_DATA_TEXT)
        ;

        $this->object->preUpdate();
    }

    public function testGetSet()
    {
        $this->assertSame(self::TEST_DATA_ID, $this->object->getId());
        $this->assertInstanceOf(\DateTime::class, $this->object->getCreatedAt());
        $this->assertInstanceOf(\DateTime::class, $this->object->getUpdatedAt());
        $this->assertSame(self::TEST_DATA_FILE, $this->object->getFile());
        $this->assertSame(Translation::FLAG_UPDATED, $this->object->getFlag());

        $this->object->setFlag(Translation::FLAG_NEW);
        $this->assertSame(Translation::FLAG_NEW, $this->object->getFlag());
    }

    public function testGetTranslationModel()
    {
        $translationModel = $this->object->getTranslationModel(self::TEST_DATA_ID);
        $this->assertInstanceOf(\Kunstmaan\TranslatorBundle\Model\Translation::class, $translationModel);
        $this->assertSame(self::TEST_DATA_KEYWORD, $translationModel->getKeyword());
        $this->assertSame(self::TEST_DATA_DOMAIN, $translationModel->getDomain());

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

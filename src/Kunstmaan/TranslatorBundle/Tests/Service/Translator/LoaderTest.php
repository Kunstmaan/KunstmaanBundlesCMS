<?php

namespace Kunstmaan\TranslatorBundle\Tests\Service\Translator;

use Kunstmaan\TranslatorBundle\Entity\Translation;
use Kunstmaan\TranslatorBundle\Repository\TranslationRepository;
use Kunstmaan\TranslatorBundle\Service\Translator\Loader;
use PHPUnit\Framework\TestCase;

class LoaderTest extends TestCase
{
    const TEST_DATA_DOMAIN = 'validation';
    const TEST_DATA_LOCALE = 'en';
    const TEST_DATA_KEYWORD = 'validation.ok';
    const TEST_DATA_TEXT = 'Everything ok';
    private Loader $loader;

    private Loader $loader;

    public function setUp(): void
    {
        $translation = new Translation();
        $translation
            ->setDomain(self::TEST_DATA_DOMAIN)
            ->setLocale(self::TEST_DATA_LOCALE)
            ->setKeyword(self::TEST_DATA_KEYWORD)
            ->setText(self::TEST_DATA_TEXT)
        ;

        $translationRepository = $this->createMock(TranslationRepository::class);
        $translationRepository->method('findBy')->willReturn([$translation]);

        $this->loader = new Loader();
        $this->loader->setTranslationRepository($translationRepository);
    }

    public function testLoad()
    {
        $catalogue = $this->loader->load('', self::TEST_DATA_LOCALE, self::TEST_DATA_DOMAIN);
        $messages = $catalogue->all(self::TEST_DATA_DOMAIN);
        $this->assertEquals(self::TEST_DATA_TEXT, $messages[self::TEST_DATA_KEYWORD]);
        $this->assertEquals(self::TEST_DATA_LOCALE, $catalogue->getLocale());
    }
}

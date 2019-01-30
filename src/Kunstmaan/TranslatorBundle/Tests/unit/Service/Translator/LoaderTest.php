<?php

namespace Kunstmaan\TranslatorBundle\Tests\Service\Translator;

use Codeception\Test\Unit;
use Kunstmaan\TranslatorBundle\Entity\Translation;
use Kunstmaan\TranslatorBundle\Repository\TranslationRepository;
use Kunstmaan\TranslatorBundle\Service\Translator\Loader;

class LoaderTest extends Unit
{
    const TEST_DATA_DOMAIN = 'validation';
    const TEST_DATA_LOCALE = 'en';
    const TEST_DATA_KEYWORD = 'validation.ok';
    const TEST_DATA_TEXT = 'Everything ok';

    public function _before()
    {
        $translation = new Translation();
        $translation
            ->setDomain(self::TEST_DATA_DOMAIN)
            ->setLocale(self::TEST_DATA_LOCALE)
            ->setKeyword(self::TEST_DATA_KEYWORD)
            ->setText(self::TEST_DATA_TEXT)
        ;

        $translationRepository = $this->makeEmpty(TranslationRepository::class, [
           'findBy' => [$translation],
        ]);

        /* @var Loader loader */
        $this->loader = new Loader();
        $this->loader->setTranslationRepository($translationRepository);
    }

    public function testLoad()
    {
        $catalogue = $this->loader->load('', self::TEST_DATA_LOCALE, self::TEST_DATA_DOMAIN);
        $messages = $catalogue->all(self::TEST_DATA_DOMAIN);
        $this->assertEquals($messages[self::TEST_DATA_KEYWORD], self::TEST_DATA_TEXT);
        $this->assertEquals($catalogue->getLocale(), self::TEST_DATA_LOCALE);
    }
}

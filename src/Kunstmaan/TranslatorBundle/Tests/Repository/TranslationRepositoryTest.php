<?php

namespace Kunstmaan\TranslatorBundle\Tests\Repository;

use Kunstmaan\TranslatorBundle\Entity\Translation;
use Kunstmaan\TranslatorBundle\Repository\TranslationRepository;
use Kunstmaan\TranslatorBundle\Tests\WebTestCase;

class TranslationRepositoryTest extends WebTestCase
{
    private ?object $translationRepository = null;

    public function setUp(): void
    {
        static::bootKernel(['test_case' => 'TranslatorBundleTest', 'root_config' => 'config.yaml']);
        $container = static::$kernel->getContainer();
        static::loadFixtures($container);

        $this->translationRepository = $container->get('kunstmaan_translator.repository.translation');
    }

    /**
     * @group translation-repository
     */
    public function testGetAllDomainsByLocale()
    {
        $result = $this->translationRepository->getAllDomainsByLocale();
        $firstItem = reset($result);
        $this->assertArrayHasKey('locale', $firstItem);
        $this->assertArrayHasKey('name', $firstItem);
    }

    /**
     * @group translation-repository
     */
    public function testGetLastChangedTranslationDate()
    {
        $date = $this->translationRepository->getLastChangedTranslationDate();
        $this->assertInstanceOf('\DateTime', $date);
    }

    /**
     * @group translation-repository
     */
    public function testGetTranslationsByLocalesAndDomains()
    {
        $result = $this->translationRepository->getTranslationsByLocalesAndDomains(['nl'], ['messages']);
        $this->assertInstanceOf(Translation::class, $result[0]);
        $this->assertGreaterThan(0, is_countable($result) ? \count($result) : 0);
    }

    /**
     * @group translation-repository
     */
    public function testFindAllNotDisabled()
    {
        $result = $this->translationRepository->findAllNotDisabled('nl');
        $this->assertInstanceOf(Translation::class, $result[0]);
        $this->assertGreaterThan(0, \count($result));
    }

    /**
     * @group translation-repository
     */
    public function testFindAllDeprecated()
    {
        $result = $this->translationRepository->findDeprecatedTranslationsBeforeDate(new \DateTime('+5 minutes'), 'messages');
        $this->assertInstanceOf(Translation::class, $result[0]);
        $this->assertGreaterThan(0, is_countable($result) ? \count($result) : 0);
    }
}

<?php

namespace Kunstmaan\TranslatorBundle\Tests\Service\Importer;

use Kunstmaan\TranslatorBundle\Repository\TranslationRepository;
use Kunstmaan\TranslatorBundle\Tests\unit\WebTestCase;

class TranslationRepositoryTest extends WebTestCase
{
    /** @var TranslationRepository */
    private $translationRepository;

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
        $result = $this->translationRepository->getTranslationsByLocalesAndDomains(array('nl'), array('messages'));
        $this->assertInstanceOf('Kunstmaan\TranslatorBundle\Entity\Translation', $result[0]);
        $this->assertGreaterThan(0, \count($result));
    }

    /**
     * @group translation-repository
     */
    public function testFindAllNotDisabled()
    {
        $result = $this->translationRepository->findAllNotDisabled('nl');
        $this->assertInstanceOf('Kunstmaan\TranslatorBundle\Entity\Translation', $result[0]);
        $this->assertGreaterThan(0, \count($result));
    }

    /**
     * @group translation-repository
     */
    public function testFindAllDeprecated()
    {
        $result = $this->translationRepository->findDeprecatedTranslationsBeforeDate(new \DateTime('+5 minutes'), 'messages');
        $this->assertInstanceOf('Kunstmaan\TranslatorBundle\Entity\Translation', $result[0]);
        $this->assertGreaterThan(0, \count($result));
    }
}

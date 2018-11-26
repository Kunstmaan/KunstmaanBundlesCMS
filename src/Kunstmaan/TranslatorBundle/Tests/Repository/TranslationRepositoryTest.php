<?php

namespace Kunstmaan\TranslatorBundle\Tests\Service\Importer;

use Kunstmaan\TranslatorBundle\Tests\BaseTestCase;

class TranslationRepositoryTest extends BaseTestCase
{
    private $translationRepository;

    public function setUp()
    {
        parent::setUp();
        $this->translationRepository = $this->getContainer()->get('kunstmaan_translator.repository.translation');
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
        $this->assertGreaterThan(0, count($result));
    }
}

<?php
namespace Kunstmaan\TranslatorBundle\Tests\Service\Importer;

use Kunstmaan\TranslatorBundle\Tests\BaseTestCase;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Finder\Finder;

class TranslationRepositoryTest extends BaseTestCase
{

    private $translationRepository;

    public function setUp()
    {
        parent::setUp();
        $this->translationRepository = $this->getContainer()->get('kunstmaan_translator.repository.translation');
    }

    public function testGetAllDomainsByLocale()
    {
        $result = $this->translationRepository->getAllDomainsByLocale();
        $firstItem = reset($result);
        $this->assertArrayHasKey('locale', $firstItem);
        $this->assertArrayHasKey('name', $firstItem);
    }
}
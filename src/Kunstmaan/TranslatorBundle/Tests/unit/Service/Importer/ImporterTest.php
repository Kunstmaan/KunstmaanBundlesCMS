<?php

namespace Kunstmaan\TranslatorBundle\Tests\Service\Importer;

use Kunstmaan\TranslatorBundle\Tests\unit\WebTestCase;
use Symfony\Component\Finder\Finder;

class ImporterTest extends WebTestCase
{
    private $rootDir;

    private $importer;

    private $translationRepository;

    public function setUp()
    {
        static::bootKernel(['test_case' => 'TranslatorBundleTest', 'root_config' => 'config.yaml']);
        $container = static::$kernel->getContainer();
        static::loadFixtures($container);

        $this->translationRepository = $container->get('kunstmaan_translator.repository.translation');
        $this->importer = $container->get('kunstmaan_translator.service.importer.importer');
        $this->rootDir = $container->getParameter('kernel.root_dir');
    }

    /**
     * @group importer
     */
    public function testImportNewDomainFileForced()
    {
        foreach ($this->getNewDomainTestFinder() as $file) {
            $this->importer->import($file, true);
        }

        $translation = $this->translationRepository->findOneBy(['keyword' => 'newdomain.name', 'locale' => 'de']);
        $this->assertEquals('a new domain', $translation->getText());
    }

    /**
     * @group importer
     */
    public function testImportExistingDomainFileNonForced()
    {
        foreach ($this->getExistingDomainTestFinder() as $file) {
            $this->importer->import($file, false);
        }

        $translation = $this->translationRepository->findOneBy(['keyword' => 'headers.frontpage', 'locale' => 'en']);
        $this->assertEquals('a not yet updated frontpage header', $translation->getText());
    }

    /**
     * @group importer-isolated
     */
    public function testImportExistingDomainFileForced()
    {
        foreach ($this->getExistingDomainTestFinder() as $file) {
            $this->importer->import($file, true);
        }

        $translation = $this->translationRepository->findOneBy(['keyword' => 'headers.frontpage', 'locale' => 'en']);
        $this->assertEquals('FrontPage', $translation->getText());
    }

    public function getNewDomainTestFinder()
    {
        $finder = new Finder();

        $finder->files()
                ->name('newdomain.de.yml')
                ->in($this->rootDir . '/Resources/translations/');

        return $finder;
    }

    public function getExistingDomainTestFinder()
    {
        $finder = new Finder();

        $finder->files()
                ->name('messages.en.yml')
                ->in($this->rootDir . '/Resources/translations/');

        return $finder;
    }
}

<?php

namespace Kunstmaan\TranslatorBundle\Tests\Service\Importer;

use Kunstmaan\TranslatorBundle\Tests\WebTestCase;
use Symfony\Component\Finder\Finder;

class ImporterTest extends WebTestCase
{
    private bool|string|int|float|\UnitEnum|array|null $rootDir = null;

    private ?object $importer = null;

    private ?object $translationRepository = null;

    public function setUp(): void
    {
        static::bootKernel(['test_case' => 'TranslatorBundleTest', 'root_config' => 'config.yaml']);
        $container = static::$kernel->getContainer();
        static::loadFixtures($container);

        $this->translationRepository = $container->get('kunstmaan_translator.repository.translation');
        $this->importer = $container->get('kunstmaan_translator.service.importer.importer');
        $this->rootDir = $container->getParameter('kernel.project_dir');
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
        $this->assertSame('a new domain', $translation->getText());
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
        $this->assertSame('a not yet updated frontpage header', $translation->getText());
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
        $this->assertSame('FrontPage', $translation->getText());
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

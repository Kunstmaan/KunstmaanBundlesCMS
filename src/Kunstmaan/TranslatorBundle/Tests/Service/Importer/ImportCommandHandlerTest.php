<?php

namespace Kunstmaan\TranslatorBundle\Tests\Service\Importer;

use Kunstmaan\TranslatorBundle\Model\Import\ImportCommand;
use Kunstmaan\TranslatorBundle\Tests\WebTestCase;

class ImportCommandHandlerTest extends WebTestCase
{
    private $importCommandHandler;

    public function setUp(): void
    {
        static::bootKernel(['test_case' => 'TranslatorBundleTest', 'root_config' => 'config.yaml']);
        $container = static::$kernel->getContainer();
        static::loadFixtures($container);

        $this->importCommandHandler = $container->get('kunstmaan_translator.service.importer.command_handler');
    }

    /**
     * @group handler
     */
    public function testExecuteImportCommand()
    {
        $importCommand = new ImportCommand();
        $importCommand
            ->setForce(false)
            ->setLocales(false)
            ->setGlobals(true)
            ->setDefaultBundle(false);

        $this->assertEquals(7, $this->importCommandHandler->executeImportCommand($importCommand));
    }

    public function testdetermineLocalesToImport()
    {
        $importCommand = new ImportCommand();
        $importCommand
            ->setForce(false)
            ->setLocales(false)
            ->setGlobals(true)
            ->setDefaultBundle(false);

        $locales = $this->importCommandHandler->determineLocalesToImport($importCommand);
        $this->assertEquals(['nl', 'en', 'de'], $locales);
    }

    public function testParseRequestedLocalesMulti()
    {
        $locale = 'nl,De,   FR';
        $expectedArray = ['nl', 'de', 'fr'];
        $locales = $this->importCommandHandler->parseRequestedLocales($locale);
        $this->assertEquals($expectedArray, $locales);
    }

    public function testParseRequestedLocalesSingle()
    {
        $locale = 'dE';
        $expectedArray = ['de'];
        $locales = $this->importCommandHandler->parseRequestedLocales($locale);
        $this->assertEquals($expectedArray, $locales);
    }

    public function testParseRequestedLocalesArray()
    {
        $locale = ['dE', 'NL', 'es'];
        $expectedArray = ['de', 'nl', 'es'];
        $locales = $this->importCommandHandler->parseRequestedLocales($locale);
        $this->assertEquals($expectedArray, $locales);
    }

    public function testImportSf4TranslationFiles()
    {
        $importCommand = new ImportCommand();
        $importCommand
            ->setForce(false)
            ->setLocales(false)
            ->setGlobals(true)
            ->setDefaultBundle('own');

        $this->assertEquals(7, $this->importCommandHandler->importBundleTranslationFiles($importCommand));
    }
}

<?php

namespace Kunstmaan\TranslatorBundle\Tests\Service\Importer;

use Kunstmaan\TranslatorBundle\Model\Import\ImportCommand;
use Kunstmaan\TranslatorBundle\Tests\unit\WebTestCase;
use Symfony\Component\HttpKernel\Kernel;

class ImportCommandHandlerTest extends WebTestCase
{
    private $importCommandHandler;

    public function setUp()
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
        $this->assertEquals(array('nl', 'en', 'de'), $locales);
    }

    public function testParseRequestedLocalesMulti()
    {
        $locale = 'nl,De,   FR';
        $expectedArray = array('nl', 'de', 'fr');
        $locales = $this->importCommandHandler->parseRequestedLocales($locale);
        $this->assertEquals($expectedArray, $locales);
    }

    public function testParseRequestedLocalesSingle()
    {
        $locale = 'dE';
        $expectedArray = array('de');
        $locales = $this->importCommandHandler->parseRequestedLocales($locale);
        $this->assertEquals($expectedArray, $locales);
    }

    public function testParseRequestedLocalesArray()
    {
        $locale = array('dE', 'NL', 'es');
        $expectedArray = array('de', 'nl', 'es');
        $locales = $this->importCommandHandler->parseRequestedLocales($locale);
        $this->assertEquals($expectedArray, $locales);
    }

    /**
     * @group legacy
     */
    public function testImportBundleTranslationFiles()
    {
        if (Kernel::VERSION_ID >= 40000) {
            $this->markTestSkipped('Skip symfony 3 test');
        }

        $importCommand = new ImportCommand();
        $importCommand
            ->setForce(false)
            ->setLocales(false)
            ->setGlobals(true)
            ->setDefaultBundle('own');

        $this->assertEquals(0, $this->importCommandHandler->importBundleTranslationFiles($importCommand));
    }

    public function testImportSf4TranslationFiles()
    {
        if (Kernel::VERSION_ID < 40000) {
            $this->markTestSkipped('Skip symfony 4 test');
        }

        $importCommand = new ImportCommand();
        $importCommand
            ->setForce(false)
            ->setLocales(false)
            ->setGlobals(true)
            ->setDefaultBundle('own');

        $this->assertEquals(7, $this->importCommandHandler->importBundleTranslationFiles($importCommand));
    }
}

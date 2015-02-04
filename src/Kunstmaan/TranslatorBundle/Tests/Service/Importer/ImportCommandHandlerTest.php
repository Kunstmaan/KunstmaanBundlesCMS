<?php
namespace Kunstmaan\TranslatorBundle\Tests\Service\Importer;

use Kunstmaan\TranslatorBundle\Tests\BaseTestCase;
use Kunstmaan\TranslatorBundle\Model\Import\ImportCommand;

class ImportCommandHandlerTest extends BaseTestCase
{

    private $importCommandHandler;

    public function setUp()
    {
        parent::setUp();
        $this->importCommandHandler = $this->getContainer()->get('kunstmaan_translator.service.importer.command_handler');
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

        $this->importCommandHandler->executeImportCommand($importCommand);
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
        $this->assertEquals(array('nl','en','de'), $locales);
    }

    public function testParseRequestedLocalesMulti()
    {
        $locale = 'nl,De,   FR';
        $expectedArray = array('nl','de','fr');
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
        $locale = array('dE','NL','es');
        $expectedArray = array('de','nl','es');
        $locales = $this->importCommandHandler->parseRequestedLocales($locale);
        $this->assertEquals($expectedArray, $locales);
    }

    public function testImportGlobalTranslationFiles()
    {

    }

    public function testImportBundleTranslationFiles()
    {
        $importCommand = new ImportCommand();
        $importCommand
            ->setForce(false)
            ->setLocales(false)
            ->setGlobals(true)
            ->setDefaultBundle('own');

        $this->importCommandHandler->importBundleTranslationFiles($importCommand);
    }
}

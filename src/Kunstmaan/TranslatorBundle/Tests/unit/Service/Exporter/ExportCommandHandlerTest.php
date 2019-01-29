<?php

namespace Kunstmaan\TranslatorBundle\Tests\Service\Exporter;

use Kunstmaan\TranslatorBundle\Model\Export\ExportCommand;
use Kunstmaan\TranslatorBundle\Tests\unit\WebTestCase;

class ExportCommandHandlerTest extends WebTestCase
{
    private $exportCommandHandler;

    public function setUp()
    {
        static::bootKernel(['test_case' => 'TranslatorBundleTest', 'root_config' => 'config.yaml']);
        $container = static::$kernel->getContainer();
        static::loadFixtures($container);

        $this->exportCommandHandler = $container->get('kunstmaan_translator.service.exporter.command_handler');
    }

    public function testGetExportFiles()
    {
        $exportCommand = new ExportCommand();
        $exportCommand
                ->setDomains(false)
                ->setLocales(false)
                ->setFormat('yml');

        $this->exportCommandHandler->getExportFiles($exportCommand);
    }
}

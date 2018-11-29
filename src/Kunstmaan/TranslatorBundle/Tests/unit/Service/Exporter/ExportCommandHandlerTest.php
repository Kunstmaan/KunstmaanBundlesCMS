<?php

namespace Kunstmaan\TranslatorBundle\Tests\Service\Exporter;

use Kunstmaan\TranslatorBundle\Model\Export\ExportCommand;
use Kunstmaan\TranslatorBundle\Tests\unit\BaseTestCase;

class ExportCommandHandlerTest extends BaseTestCase
{
    private $exportCommandHandler;

    public function setUp()
    {
        parent::setUp();
        $this->exportCommandHandler = $this->getContainer()->get('kunstmaan_translator.service.exporter.command_handler');
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

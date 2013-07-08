<?php

namespace Kunstmaan\TranslatorBundle\Service\Importer;

use Kunstmaan\TranslatorBundle\Model\Export\ExportCommand;
use Symfony\Component\Finder\Finder;

/**
 * Parses an ExportCommand
 */
class ExportCommandHandler
{

    /**
     * Exporter
     * @var Kunstmaan\TranslatorBundle\Service\Exporter\Exporter
     */
    private $exporter;

    /**
     * Execute an export command
     * @param  ExportCommand $exportCommand
     * @return int           total number of files imported
     */
    public function export(ExportCommand $exportCommand)
    {

    }

    public function setExporter($exporter)
    {
        $this->exporter = $exporter;
    }
}

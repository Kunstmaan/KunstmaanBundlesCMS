<?php
namespace Kunstmaan\TranslatorBundle\Service\Exporter;

use Kunstmaan\TranslatorBundle\Model\Export\ExportFile;

/**
 * Responsible for exporting translations into files
 */
class Exporter
{
    /**
     * Array of all translation exporter
     * @var array
     */
    private $exporters = array();

    /**
     * Stasher for saving data into a resource
     * @var Kunstmaan\TranslatorBundle\Service\Stasher\StasherInterface
     */
    private $stasher;

    public function getExportedContent(ExportFile $exportFile)
    {
        return $this->getExporterByExtension($exportFile->getExtension())->export($exportFile->getArray());
    }

    public function getExporterByExtension($extension)
    {
        foreach ($this->exporters as $exporter) {
            if ($exporter->supports($extension)) {
                return $exporter;
            }
        }

        throw new \Exception(sprintf('No %s file exporter found or defined.', $extension));
    }

    public function setExporters($exporters)
    {
        $this->exporters = $exporters;
    }

}

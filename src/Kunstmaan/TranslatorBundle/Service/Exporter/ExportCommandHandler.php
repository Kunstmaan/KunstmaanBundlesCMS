<?php

namespace Kunstmaan\TranslatorBundle\Service\Exporter;

use Kunstmaan\TranslatorBundle\Model\Export\ExportCommand;
use Doctrine\Common\Collections\ArrayCollection;
use Kunstmaan\TranslatorBundle\Model\Export\ExportFile;
/**
 * Parses an ExportCommand
 */
class ExportCommandHandler extends \Kunstmaan\TranslatorBundle\Service\AbstractCommandHandler
{

    /**
     * Exporter
     * @var Kunstmaan\TranslatorBundle\Service\Exporter\Exporter
     */
    private $exporter;

    /**
     * Stasher
     * @var Kunstmaan\TranslatorBundle\Service\Stasher\StasherInterface
     */
    private $stasher;

    /**
     * Execute an export command
     * @param  ExportCommand $exportCommand
     * @return int           total number of files imported
     */
    public function executeExportCommand(ExportCommand $exportCommand)
    {
        $exportFiles = $this->getExportFiles($exportCommand);
        $exportFiles = $this->fillExportFilesContent($exportFiles);

        // haal alle vertalingen per domain op, sorteer per file
        // een file heeft een domain locale en extension, daaronder ziten alle keywords + arraycollection met vertalingen
    }

    /**
     * Convert an exportCommand into an array of ExportFiles
     * @param  ExportCommand $exportCommand
     * @return array                       an array of ExportFiles (without filecontent filled in)
     */
    public function getExportFiles(ExportCommand $exportCommand)
    {
        $locales = $this->determineLocalesToImport($exportCommand);
        $domains = $this->determineDomainsToImport($exportCommand);
        $translations = $this->stasher->getTranslationsByLocalesAndDomains($locales, $domains);

        $translationFiles = new ArrayCollection;

        foreach ($translations as $translation) {
            $exportFileKey = $translation->getDomain()->getName() . '.' . $translation->getLocale().'.'.$exportCommand->getFormat();

            if (!$translationFiles->containsKey($exportFileKey)) {
                $exportFile = new ExportFile;
                $exportFile->setExtension($exportCommand->getFormat());
                $exportFile->setDomain($translation->getDomain()->getName());
                $exportFile->setLocale($translation->getLocale());
                $translationFiles->set($exportFileKey, $exportFile);
            }

            $translationFiles->get($exportFileKey)->addTranslation($translation);

        }

        return $translationFiles;
    }

    public function fillExportFilesContent(ArrayCollection $exportFiles)
    {
        foreach ($exportFiles as $exportFile) {
            $exportFile->fillArray();
            $content = $this->exporter->getExportedContent($exportFile);
            $exportFile->setContent($content);
            var_dump($content);
        }
    }

    /**
     * Returns an array with all languages that needs to be imported (from the given ExportCommand)
     * If non is given, all managed locales will be used (defined in config)
     * @param  ExportCommand $exportCommand
     * @return array         all locales to import by the given ExportCommand
     */
    public function determineLocalesToImport(ExportCommand $exportCommand)
    {
        if ($exportCommand->getLocales() === false) {
            return $this->managedLocales;
        }

        return $this->parseRequestedLocales($exportCommand->getLocales());
    }

    public function determineDomainsToImport(ExportCommand $exportCommand)
    {
        if ($exportCommand->getDomains() === false) {
            return array();
        }

        return $this->parseRequestedDomains($exportCommand->getDomains());
    }

    public function setExporter($exporter)
    {
        $this->exporter = $exporter;
    }

    public function setStasher($stasher)
    {
        $this->stasher = $stasher;
    }
}

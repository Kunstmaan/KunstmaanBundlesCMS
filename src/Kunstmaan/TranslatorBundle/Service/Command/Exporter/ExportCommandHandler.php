<?php

namespace Kunstmaan\TranslatorBundle\Service\Command\Exporter;

use Doctrine\Common\Collections\ArrayCollection;
use Kunstmaan\TranslatorBundle\Entity\Translation;
use Kunstmaan\TranslatorBundle\Model\Export\ExportCommand;
use Kunstmaan\TranslatorBundle\Model\Export\ExportFile;
use Kunstmaan\TranslatorBundle\Repository\TranslationRepository;
use Kunstmaan\TranslatorBundle\Service\Command\AbstractCommandHandler;
use Symfony\Component\HttpFoundation\Response;

/**
 * Parses an ExportCommand
 */
class ExportCommandHandler extends AbstractCommandHandler
{
    /**
     * @var Exporter
     */
    private $exporter;

    /**
     * @var TranslationRepository
     */
    private $translationRepository;

    /**
     * Execute an export command.
     * Get all translations per domain and sort on file.
     * A File has a domain locale, an extensions, with keywords and a ArrayCollection with translations.
     *
     * @param ExportCommand $exportCommand
     *
     * @return int total number of files imported
     */
    public function executeExportCommand(ExportCommand $exportCommand)
    {
        $exportFiles = $this->getExportFiles($exportCommand);
        $this->fillExportFilesContent($exportFiles);
    }

    /**
     * Execute an export to CSV command.
     *
     * @param ExportCommand $exportCommand
     *
     * @return string|Response
     */
    public function executeExportCSVCommand(ExportCommand $exportCommand)
    {
        $translations = $this->getTranslations($exportCommand);
        /** @var CSVFileExporter $exporter */
        $exporter = $this->exporter->getExporterByExtension($exportCommand->getFormat());
        $exporter->setLocales($this->determineLocalesToImport($exportCommand));

        return $exporter->export($translations);
    }

    /**
     * Convert an exportCommand into an array of translations
     *
     * @param ExportCommand $exportCommand
     *
     * @return array an array of translations
     */
    public function getTranslations(ExportCommand $exportCommand)
    {
        $locales = $this->determineLocalesToImport($exportCommand);
        $domains = $this->determineDomainsToImport($exportCommand);

        $translations = [];
        /** @var Translation $translation */
        foreach ($this->translationRepository->getTranslationsByLocalesAndDomains($locales, $domains) as $translation) {
            // Sort by translation key.
            $translations[$translation->getKeyword()][$translation->getLocale()] = $translation;
        }

        return $translations;
    }

    /**
     * Convert an exportCommand into an array of ExportFiles
     *
     * @param ExportCommand $exportCommand
     *
     * @return ArrayCollection an array of ExportFiles (without filecontent filled in)
     */
    public function getExportFiles(ExportCommand $exportCommand)
    {
        $locales = $this->determineLocalesToImport($exportCommand);
        $domains = $this->determineDomainsToImport($exportCommand);

        $translations = $this->translationRepository->getTranslationsByLocalesAndDomains($locales, $domains);

        $translationFiles = new ArrayCollection();

        /** @var Translation $translation */
        foreach ($translations as $translation) {
            $exportFileKey = $translation->getDomain() . '.' . $translation->getLocale() . '.' . $exportCommand->getFormat();

            if (!$translationFiles->containsKey($exportFileKey)) {
                $exportFile = new ExportFile();
                $exportFile->setExtension($exportCommand->getFormat());
                $exportFile->setDomain($translation->getDomain());
                $exportFile->setLocale($translation->getLocale());
                $translationFiles->set($exportFileKey, $exportFile);
            }

            $translationFiles->get($exportFileKey)->addTranslation($translation);
        }

        return $translationFiles;
    }

    /**
     * @param ArrayCollection $exportFiles
     */
    public function fillExportFilesContent(ArrayCollection $exportFiles)
    {
        foreach ($exportFiles as $exportFile) {
            $exportFile->fillArray();
            $content = $this->exporter->getExportedContent($exportFile);
            $exportFile->setContent($content);
        }
    }

    /**
     * Returns an array with all languages that needs to be imported (from the given ExportCommand)
     * If non is given, all managed locales will be used (defined in config)
     *
     * @param ExportCommand $exportCommand
     *
     * @return array all locales to import by the given ExportCommand
     */
    public function determineLocalesToImport(ExportCommand $exportCommand)
    {
        if ($exportCommand->getLocales() === false) {
            return $this->managedLocales;
        }

        return $this->parseRequestedLocales($exportCommand->getLocales());
    }

    /**
     * @param ExportCommand $exportCommand
     *
     * @return array
     */
    public function determineDomainsToImport(ExportCommand $exportCommand)
    {
        if ($exportCommand->getDomains() === false) {
            return [];
        }

        return $this->parseRequestedDomains($exportCommand->getDomains());
    }

    /**
     * @param $exporter
     */
    public function setExporter($exporter)
    {
        $this->exporter = $exporter;
    }

    /**
     * @param $translationRepository
     */
    public function setTranslationRepository($translationRepository)
    {
        $this->translationRepository = $translationRepository;
    }
}

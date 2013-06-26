<?php

namespace Kunstmaan\TranslatorBundle\Service\Importer;

use Kunstmaan\TranslatorBundle\Model\Import\ImportCommand;
use Symfony\Component\Finder\Finder;


class ImportCommandHandler
{

    private $managedLocales;
    private $kernel;
    private $translationFileExplorer;

    public function executeImportCommand(ImportCommand $importCommand)
    {
        // bekijk path, 1 of meerdere files
        // bekijk per file welke soort het is
        // domain.taal.extensie parsen
        // bekijken met language of de taal toegevoegd kan worden
        // bekijk met overwrite of een bestaande mag gewijzigd worden (replace into)
        // bekijk of het domain al bestaat

        if($importCommand->getBundle() === false) {
            return $this->importGlobalTranslationFiles($importCommand);
        }

        return $this->importBundleTranslationFiles($importCommand);
    }

    public function importGlobalTranslationFiles(ImportCommand $importCommand)
    {
        $finder = $this->translationFileExplorer->find($this->kernel->getRootDir(), $this->determineLocalesToImport($importCommand));

    }

    public function importBundleTranslationFiles(ImportCommand $importCommand)
    {
        $this->validateBundleName($importCommand->getBundle());
        $bundles = array_change_key_case($this->kernel->getBundles(), CASE_LOWER);
        $finder = $this->translationFileExplorer->find($bundles[$importCommand->getBundle()]->getPath(), $this->determineLocalesToImport($importCommand));
    }

    public function validateBundleName($bundle) {
        // get bundle names and strtolower them
        $bundles = array_map('strtolower', array_keys($this->kernel->getBundles()));

        if (in_array(strtolower(trim($bundle)), $bundles)) {
            return true;
        }

        throw new \Exception(sprintf('bundle "%s" not found in available bundles: %s', $bundle, implode(', ', $bundles)));
    }


    public function determineLocalesToImport(ImportCommand $importCommand)
    {
        if ($importCommand->getLocale() === false) {
            return $this->managedLocales;
        }

        return $this->parseRequestedLocales($importCommand->getLocale());
    }

    public function parseRequestedLocales($locales)
    {
        if (!is_array($locales) && strpos($locales, ',') === false && mb_strlen(trim($locales)) == 2) {
            return array(strtolower(trim($locales)));
        }

        if (!is_array($locales)){
            $locales = explode(',', $locales);
        }

        if (count($locales) >= 1) {
            return array_map(function($locale) { return strtolower(trim($locale)); }, $locales);
        }

        throw new \Exception('Invalid locales specified');
    }

    public function setManagedLocales($managedLocales)
    {
        $this->managedLocales = $managedLocales;
    }

    public function setKernel($kernel)
    {
        $this->kernel = $kernel;
    }

    public function setTranslationFileExplorer($translationFileExplorer)
    {
        $this->translationFileExplorer = $translationFileExplorer;
    }
}

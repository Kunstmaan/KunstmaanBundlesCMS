<?php

namespace Kunstmaan\TranslatorBundle\Service\Importer;

use Kunstmaan\TranslatorBundle\Model\Import\ImportCommand;
use Symfony\Component\Finder\Finder;


class ImportCommandHandler
{

    private $managedLocales;
    private $kernel;
    private $translationFileExplorer;
    private $importer;

    public function executeImportCommand(ImportCommand $importCommand)
    {
        if($importCommand->getBundle() === false) {
            return $this->importGlobalTranslationFiles($importCommand);
        }

        return $this->importBundleTranslationFiles($importCommand);
    }

    public function importGlobalTranslationFiles(ImportCommand $importCommand)
    {
        $finder = $this->translationFileExplorer->find($this->kernel->getRootDir(), $this->determineLocalesToImport($importCommand));
        $this->importTranslationFiles($finder, $importCommand->getForce());
    }

    public function importBundleTranslationFiles(ImportCommand $importCommand)
    {
        $this->validateBundleName($importCommand->getBundle());
        $bundles = array_change_key_case($this->kernel->getBundles(), CASE_LOWER);
        $finder = $this->translationFileExplorer->find($bundles[$importCommand->getBundle()]->getPath(), $this->determineLocalesToImport($importCommand));
        $this->importTranslationFiles($finder, $importCommand->getForce());
    }

    public function importTranslationFiles(Finder $finder, $force = flase)
    {
        if (!$finder instanceof Finder) {
            throw new \Exception('No files found.');
        }

        foreach ($finder as $file) {
            $this->importer->import($file, $force);
        }
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

    public function setImporter($importer)
    {
        $this->importer = $importer;
    }
}

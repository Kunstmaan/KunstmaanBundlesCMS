<?php

namespace Kunstmaan\TranslatorBundle\Service\Command\Importer;

use Kunstmaan\TranslatorBundle\Model\Import\ImportCommand;
use Symfony\Component\Finder\Finder;

/**
 * Parses an ImportCommand
 */
class ImportCommandHandler extends \Kunstmaan\TranslatorBundle\Service\Command\AbstractCommandHandler
{

    /**
     * TranslationFileExplorer
     * @var Kunstmaan\TranslatorBundle\Service\TranslationFileExplorer
     */
    private $translationFileExplorer;

    /**
     * Importer
     * @var Kunstmaan\TranslatorBundle\Service\Importer\Importer
     */
    private $importer;

    /**
     * Execute an import command
     * @param  ImportCommand $importCommand
     * @return int           total number of files imported
     */
    public function executeImportCommand(ImportCommand $importCommand)
    {
        if ($importCommand->getBundle() === false || $importCommand->getBundle() === null) {
            return $this->importGlobalTranslationFiles($importCommand);
        }

        return $this->importBundleTranslationFiles($importCommand);
    }

    /**
     * Import all translation files from app resources
     * @param  ImportCommand $importCommand
     * @return int           total number of files imported
     */
    public function importGlobalTranslationFiles(ImportCommand $importCommand)
    {
        $finder = $this->translationFileExplorer->find($this->kernel->getRootDir(), $this->determineLocalesToImport($importCommand));

        return $this->importTranslationFiles($finder, $importCommand->getForce());
    }

    /**
     * Import all translation files from a specific bundle, bundle name will be lowercased so cases don't matter
     * @param  ImportCommand $importCommand
     * @return int           total number of files imported
     */
    public function importBundleTranslationFiles(ImportCommand $importCommand)
    {
        if (strtolower($importCommand->getBundle()) == 'all') {
            return $this->importAllBundlesTranslationFiles($importCommand);
        }

        return $this->importSingleBundleTranslationFiles($importCommand);
    }

    /**
     * Import all translation files from all registered bundles (in AppKernel)
     * @param  ImportCommand $importCommand
     * @return int           total number of files imported
     */
    public function importAllBundlesTranslationFiles(ImportCommand $importCommand)
    {
        $bundles = array_map('strtolower', array_keys($this->kernel->getBundles()));
        $imported = 0;

        foreach ($bundles as $bundle) {
            $importCommand->setBundle($bundle);
            $imported += $this->importSingleBundleTranslationFiles($importCommand);
        }

        return $imported;
    }

    /**
     * Import all translation files from a single bundle
     * @param  ImportCommand $importCommand
     * @return int           total number of files imported
     */
    public function importSingleBundleTranslationFiles(ImportCommand $importCommand)
    {
        $this->validateBundleName($importCommand->getBundle());
        $bundles = array_change_key_case($this->kernel->getBundles(), CASE_LOWER);
        $finder = $this->translationFileExplorer->find($bundles[strtolower($importCommand->getBundle())]->getPath(), $this->determineLocalesToImport($importCommand));

        if ($finder == null) {
            return 0;
        }

        return $this->importTranslationFiles($finder, $importCommand->getForce());
    }

    /**
     * Import translation files from a specific Finder object
     * The finder object shoud already have the files to look for defined;
     * Forcing the import will override all existing translations in the stasher
     * @param  Finder  $finder
     * @param  boolean $force  override identical translations in the stasher (domain/locale and keyword combination)
     * @return int     total number of files imported
     */
    public function importTranslationFiles(Finder $finder, $force = flase)
    {
        if (!$finder instanceof Finder) {
            return false;
        }

        $imported = 0;

        foreach ($finder as $file) {
            $imported += $this->importer->import($file, $force);
        }

        return $imported;
    }

    /**
     * Validates that a bundle is registered in the AppKernel
     * @param  string     $bundle
     * @return boolean    bundle is valid or not
     * @throws \Exception If the bundlename isn't valid
     */
    public function validateBundleName($bundle)
    {
        // strtolower all bundle names
        $bundles = array_map('strtolower', array_keys($this->kernel->getBundles()));

        if (in_array(strtolower(trim($bundle)), $bundles)) {
            return true;
        }

        throw new \Exception(sprintf('bundle "%s" not found in available bundles: %s', $bundle, implode(', ', $bundles)));
    }

    /**
     * Gives an array with all languages that needs to be imported (from the given ImportCommand)
     * If non is given, all managed locales will be used (defined in config)
     * @param  ImportCommand $importCommand
     * @return array         all locales to import by the given ImportCommand
     */
    public function determineLocalesToImport(ImportCommand $importCommand)
    {
        if ($importCommand->getLocales() === false) {
            return $this->managedLocales;
        }

        return $this->parseRequestedLocales($importCommand->getLocales());
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

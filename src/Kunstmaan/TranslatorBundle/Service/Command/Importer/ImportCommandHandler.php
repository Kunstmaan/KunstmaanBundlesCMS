<?php

namespace Kunstmaan\TranslatorBundle\Service\Command\Importer;

use Kunstmaan\TranslatorBundle\Model\Import\ImportCommand;
use Kunstmaan\TranslatorBundle\Service\Command\AbstractCommandHandler;
use Kunstmaan\TranslatorBundle\Service\Exception\TranslationsNotFoundException;
use Kunstmaan\TranslatorBundle\Service\TranslationFileExplorer;
use Symfony\Component\Finder\Finder;

/**
 * Parses an ImportCommand
 */
class ImportCommandHandler extends AbstractCommandHandler
{
    /**
     * TranslationFileExplorer
     *
     * @var TranslationFileExplorer
     */
    private $translationFileExplorer;

    /**
     * Importer
     *
     * @var Importer
     */
    private $importer;

    /**
     * Execute an import command
     *
     * @return int total number of files imported
     */
    public function executeImportCommand(ImportCommand $importCommand)
    {
        $amount = 0;
        $defaultBundleNotSet = $importCommand->getDefaultBundle() === false || $importCommand->getDefaultBundle() === null;

        if ($importCommand->getGlobals()) {
            $amount = $this->importGlobalTranslationFiles($importCommand);
            if ($defaultBundleNotSet) {
                return $amount;
            }
        }

        if ($defaultBundleNotSet) {
            $importCommand->setDefaultBundle('all');
        }

        $amount += $this->importBundleTranslationFiles($importCommand);

        return $amount;
    }

    /**
     * Import all translation files from app resources
     *
     * @return int total number of files imported
     */
    private function importGlobalTranslationFiles(ImportCommand $importCommand): int
    {
        $baseDir = $this->kernel->getProjectDir();
        $locales = $this->determineLocalesToImport($importCommand);
        $finder = $this->translationFileExplorer->find($baseDir, $locales, 'translations');

        return $this->importTranslationFiles($finder, $importCommand->getForce());
    }

    /**
     * Import all translation files from a specific bundle, bundle name will be lowercased so cases don't matter
     *
     * @return int total number of files imported
     */
    public function importBundleTranslationFiles(ImportCommand $importCommand)
    {
        $importBundle = strtolower($importCommand->getDefaultBundle());

        if ($importBundle === 'all') {
            $importCount = $this->importAllBundlesTranslationFiles($importCommand);
            $importCount += $this->importApplicationTranslationFiles($importCommand);

            return $importCount;
        }

        if ($importBundle === 'custom') {
            return $this->importCustomBundlesTranslationFiles($importCommand);
        }

        return $this->importApplicationTranslationFiles($importCommand);
    }

    /**
     * Import all translation files from all registered bundles (in AppKernel)
     *
     * @return int total number of files imported
     */
    private function importAllBundlesTranslationFiles(ImportCommand $importCommand): int
    {
        $bundles = array_map('strtolower', array_keys($this->kernel->getBundles()));
        $imported = 0;

        foreach ($bundles as $bundle) {
            $importCommand->setDefaultBundle($bundle);

            try {
                $imported += $this->importSingleBundleTranslationFiles($importCommand);
            } catch (TranslationsNotFoundException $e) {
                continue;
            }
        }

        return $imported;
    }

    /**
     * Import the translation files from the defined bundles.
     *
     * @return int The total number of imported files
     */
    private function importCustomBundlesTranslationFiles(ImportCommand $importCommand): int
    {
        $imported = 0;

        foreach ($importCommand->getBundles() as $bundle) {
            $importCommand->setDefaultBundle(strtolower($bundle));

            try {
                $imported += $this->importSingleBundleTranslationFiles($importCommand);
            } catch (TranslationsNotFoundException $e) {
                continue;
            }
        }

        return $imported;
    }

    /**
     * Import all translation files from a single bundle
     *
     * @return int total number of files imported
     */
    private function importSingleBundleTranslationFiles(ImportCommand $importCommand): int
    {
        $this->validateBundleName($importCommand->getDefaultBundle());
        $bundles = array_change_key_case($this->kernel->getBundles(), CASE_LOWER);
        $finder = $this->translationFileExplorer->find($bundles[strtolower($importCommand->getDefaultBundle())]->getPath(), $this->determineLocalesToImport($importCommand));

        if ($finder === null) {
            return 0;
        }

        return $this->importTranslationFiles($finder, $importCommand->getForce());
    }

    /**
     * Import translation files from a specific Finder object
     * The finder object shoud already have the files to look for defined;
     * Forcing the import will override all existing translations in the stasher
     *
     * @param bool $force override identical translations in the stasher (domain/locale and keyword combination)
     *
     * @return int total number of files imported
     */
    private function importTranslationFiles(Finder $finder, $force = false): int
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
     *
     * @param string $bundle
     *
     * @return bool bundle is valid or not
     *
     * @throws \Exception If the bundlename isn't valid
     */
    public function validateBundleName($bundle)
    {
        // strtolower all bundle names
        $bundles = array_map('strtolower', array_keys($this->kernel->getBundles()));

        if (\in_array(strtolower(trim($bundle)), $bundles)) {
            return true;
        }

        throw new \Exception(sprintf('bundle "%s" not found in available bundles: %s', $bundle, implode(', ', $bundles)));
    }

    /**
     * Gives an array with all languages that needs to be imported (from the given ImportCommand)
     * If non is given, all managed locales will be used (defined in config)
     *
     * @return array all locales to import by the given ImportCommand
     */
    public function determineLocalesToImport(ImportCommand $importCommand)
    {
        if ($importCommand->getLocales() === false || $importCommand->getLocales() === null) {
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

    private function importApplicationTranslationFiles($importCommand)
    {
        $finder = $this->translationFileExplorer->find($this->kernel->getProjectDir(), $this->determineLocalesToImport($importCommand), 'translations');

        if ($finder === null) {
            return 0;
        }

        return $this->importTranslationFiles($finder, $importCommand->getForce());
    }
}

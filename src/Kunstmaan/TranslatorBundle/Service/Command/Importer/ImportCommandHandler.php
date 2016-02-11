<?php

namespace Kunstmaan\TranslatorBundle\Service\Command\Importer;

use Kunstmaan\TranslatorBundle\Model\Import\ImportCommand;
use Kunstmaan\TranslatorBundle\Service\Command\AbstractCommandHandler;
use Kunstmaan\TranslatorBundle\Service\Exception\TranslationsNotFoundException;
use Symfony\Component\Finder\Finder;

/**
 * Parses an ImportCommand
 */
class ImportCommandHandler extends AbstractCommandHandler
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
        if ($importCommand->getDefaultBundle() === false || $importCommand->getDefaultBundle() === null) {
            return $this->importGlobalTranslationFiles($importCommand);
        }

        return $this->importBundleTranslationFiles($importCommand);
    }

    /**
     * Import all translation files from app resources
     * @param  ImportCommand $importCommand
     * @return int           total number of files imported
     */
    private function importGlobalTranslationFiles(ImportCommand $importCommand)
    {
        $locales = $this->determineLocalesToImport($importCommand);
        $finder = $this->translationFileExplorer->find($this->kernel->getRootDir(), $locales);

        return $this->importTranslationFiles($finder, $importCommand->getForce());
    }

    /**
     * Import all translation files from a specific bundle, bundle name will be lowercased so cases don't matter
     * @param  ImportCommand $importCommand
     * @return int           total number of files imported
     */
    public function importBundleTranslationFiles(ImportCommand $importCommand)
    {
        $importBundle = strtolower($importCommand->getDefaultBundle());

        if ($importBundle == 'all') {
            return $this->importAllBundlesTranslationFiles($importCommand);
        } elseif ($importBundle == 'custom') {
            return $this->importCustomBundlesTranslationFiles($importCommand);
        } else {
            return $this->importOwnBundlesTranslationFiles($importCommand);
        }
    }

    /**
     * Import all translation files from all registered bundles (in AppKernel)
     * @param  ImportCommand $importCommand
     * @return int           total number of files imported
     */
    private function importAllBundlesTranslationFiles(ImportCommand $importCommand)
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
     * Import all translation files from your own registered bundles (in src/ directory)
     * @param  ImportCommand $importCommand
     * @return int           The total number of imported files
     */
    private function importOwnBundlesTranslationFiles(ImportCommand $importCommand)
    {
        $imported = 0;
        $srcDir = dirname($this->kernel->getRootDir()) . '/src';

        foreach($this->kernel->getBundles() as $name => $bundle) {
            if (strpos($bundle->getPath(), $srcDir) !== false) {
                $importCommand->setDefaultBundle(strtolower($name));

                try {
                    $imported += $this->importSingleBundleTranslationFiles($importCommand);
                } catch (TranslationsNotFoundException $e) {
                    continue;
                }
            }
        }

        return $imported;
    }

    /**
     * Import the translation files from the defined bundles.
     * @param  ImportCommand $importCommand
     * @return int           The total number of imported files
     */
    private function importCustomBundlesTranslationFiles(ImportCommand $importCommand)
    {
        $imported = 0;

        foreach($importCommand->getBundles() as $bundle) {
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
     * @param  ImportCommand $importCommand
     * @return int           total number of files imported
     */
    private function importSingleBundleTranslationFiles(ImportCommand $importCommand)
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
     * @param  Finder  $finder
     * @param  boolean $force  override identical translations in the stasher (domain/locale and keyword combination)
     * @return int     total number of files imported
     */
    private function importTranslationFiles(Finder $finder, $force = flase)
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
}

<?php
namespace Kunstmaan\TranslatorBundle\Service\Importer;

use Kunstmaan\TranslatorBundle\Model\Translation\Translation;
use Kunstmaan\TranslatorBundle\Model\Translation\TranslationGroup;

/**
 * Responsible for importing translation files into the stasher by defined loaders
 */
class Importer
{
    /**
     * Array of all translation loaders
     * @var array
     */
    private $loaders = array();

    /**
     * Stasher for saving data into a resource
     * @var Kunstmaan\TranslatorBundle\Service\Stasher\StasherInterface
     */
    private $stasher;

    /**
     * TranslationGroupManager
     * @var Kunstmaan\TranslatorBundle\Service\TranslationGroupManager
     */
    private $translationGroupManager;

    /**
     * Import translation files into the stasher from the given file source
     * @param  \Symfony\Component\Finder\SplFileInfo $file
     * @param  boolean                               $force override simular translations in the stasher with the one from the file
     * @return int                                   number of single translations imported into stasher
     * @throws \Exception                            If the requested file has no loader registered
     */
    public function import(\Symfony\Component\Finder\SplFileInfo $file, $force = false)
    {
        $this->validateLoaders($this->loaders);

        $filename = $file->getFilename();
        list($domain, $locale, $extension) = explode('.', $filename);

        if (! isset($this->loaders[$extension]) || !$this->loaders[$extension] instanceof \Symfony\Component\Translation\Loader\LoaderInterface) {
            throw new \Exception(sprintf('Requested loader for extension .%s isnt set', $extension));
        }

        $loader = $this->loaders[$extension];
        $messageCatalogue = $loader->load($file->getPathname(), $locale, $domain);
        $importedTranslations = 0;

        foreach ($messageCatalogue->all($domain) as $keyword => $text) {
            $this->importSingleTranslation($keyword, $text, $locale, $filename, $domain, $force);
            $importedTranslations++;
        }

        $this->stasher->flush();

        return $importedTranslations;
    }

    /**
     * Import a single translation into the stasher
     * @param  string                                                    $keyword
     * @param  string                                                    $text
     * @param  string                                                    $locale
     * @param  string                                                    $filename
     * @param  string                                                    $domain
     * @param  boolean                                                   $force    override simular translation in the stasher
     * @return \Kunstmaan\TranslatorBundle\Model\Translation\Translation the imported translation
     */
    private function importSingleTranslation($keyword, $text, $locale, $filename, $domain, $force = false)
    {
        $translationGroup = $this->stasher->getTranslationGroupByKeywordAndDomain($keyword, $domain);

        if (!($translationGroup instanceof TranslationGroup)) {
            $translationGroup = $this->translationGroupManager->create($keyword, $domain);
        }

        $translation = $this->translationGroupManager->addTranslation($translationGroup, $locale, $text, $filename);

        if ($force === true && ! $translation instanceof Translation) {
            $translation = $this->translationGroupManager->updateTranslation($translationGroup, $locale, $text, $filename);
        }

        return $translation;
    }

    /**
     * Validate the loaders
     * @param  array      $loaders
     * @return void
     * @throws \Exception If no loaders are defined
     */
    public function validateLoaders($loaders = array())
    {
        if (!is_array($loaders) || count($loaders) <= 0) {
            throw new \Exception('No translation file loaders tagged.');
        }
    }

    public function setLoaders(array $loaders)
    {
        $this->loaders = $loaders;
    }

    public function setStasher($stasher)
    {
        $this->stasher = $stasher;
    }

    public function setTranslationGroupManager($translationGroupManager)
    {
        $this->translationGroupManager = $translationGroupManager;
    }
}

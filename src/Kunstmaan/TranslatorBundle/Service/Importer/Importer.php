<?php
namespace Kunstmaan\TranslatorBundle\Service\Importer;

use Kunstmaan\TranslatorBundle\Model\Translation\Translation;
use Kunstmaan\TranslatorBundle\Model\Translation\TranslationGroup;

class Importer
{

    private $loaders = array();
    private $stasher;
    private $translationGroupManager;

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

        foreach ($messageCatalogue->all($domain) as $keyword => $text) {
            $this->importSingleTranslation($keyword, $text, $locale, $filename, $domain, $force);
        }

        $this->stasher->flush();
    }

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
     * Checks if this is a valid array of loaders
     */
    public function validateLoaders($loaders = array())
    {
        if(!is_array($loaders) || count($loaders) <= 0) {
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
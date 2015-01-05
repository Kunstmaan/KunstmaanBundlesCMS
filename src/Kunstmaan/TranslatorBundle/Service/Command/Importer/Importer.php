<?php
namespace Kunstmaan\TranslatorBundle\Service\Command\Importer;

use Kunstmaan\TranslatorBundle\Model\Translation\Translation;
use Kunstmaan\TranslatorBundle\Model\Translation\TranslationGroup;

class Importer
{
    /**
     * Array of all translation loaders
     * @var array
     */
    private $loaders = array();

    private $translationRepository;

    /**
     * TranslationGroupManager
     * @var Kunstmaan\TranslatorBundle\Service\TranslationGroupManager
     */
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
        $importedTranslations = 0;

        foreach ($messageCatalogue->all($domain) as $keyword => $text) {
            if ($this->importSingleTranslation($keyword, $text, $locale, $filename, $domain, $force)) {
                $importedTranslations++;
                $this->translationRepository->flush();
            }
        }

        return $importedTranslations;
    }

    private function importSingleTranslation($keyword, $text, $locale, $filename, $domain, $force = false)
    {
        $translationGroup = $this->translationGroupManager->getTranslationGroupByKeywordAndDomain($keyword, $domain);

        if (!($translationGroup instanceof TranslationGroup)) {
            $translationGroup = $this->translationGroupManager->create($keyword, $domain);
        }

        $translation = $this->translationGroupManager->addTranslation($translationGroup, $locale, $text, $filename);

        if (null === $translation && false === $force) {
            return false;
        }

        if (true === $force && null === $translation) {
            $translation = $this->translationGroupManager->updateTranslation($translationGroup, $locale, $text, $filename);

            return true;
        }

        return true;
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

    public function setTranslationRepository($translationRepository)
    {
        $this->translationRepository = $translationRepository;
    }

    public function setTranslationGroupManager($translationGroupManager)
    {
        $this->translationGroupManager = $translationGroupManager;
    }

}

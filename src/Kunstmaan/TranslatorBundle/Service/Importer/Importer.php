<?php
namespace Kunstmaan\TranslatorBundle\Service\Importer;

use Kunstmaan\TranslatorBundle\Entity\Translation;
use Kunstmaan\TranslatorBundle\Entity\TranslationDomain;
use Kunstmaan\TranslatorBundle\Model\Translation\TranslationGroup;

class Importer
{

    private $loaders = array();
    private $stasher;

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

        $translationDomain = $this->stasher->getTranslationDomainByName($domain);

        if (!($translationDomain instanceof TranslationDomain)) {
            $translationDomain = $this->stasher->createTranslationDomain($domain);
            $this->stasher->flush($translationDomain);
        }

        foreach ($messageCatalogue->all($domain) as $keyword => $text) {
            $this->importSingleTranslation($keyword, $text, $locale, $filename, $translationDomain, $force);
        }

        $this->stasher->flush();

    }

    private function importSingleTranslation($keyword, $text, $locale, $filename, $translationDomain, $force = false)
    {
        $translationGroup = $this->stasher->getTranslationGroupByKeywordAndDomain($keyword, $translationDomain);

        if (!($translationGroup instanceof TranslationGroup)) {
            $translationGroup = new TranslationGroup();
            $translationGroup->setKeyword($keyword);
            $translationGroup->setTranslationDomain($translationDomain);
        }

        $translation = $this->addTranslationIfNew($translationGroup, $locale, $text, $filename);

        if ($force === true && ! $translation instanceof Translation) {
            $translation = $this->updateTranslation($translationGroup, $locale, $text, $filename);
        }

        return $translation;
    }


    /**
     * Update an existing translation
     */
    public function updateTranslation(TranslationGroup $translationGroup, $locale, $text, $filename)
    {
        $translation = $translationGroup->getTranslationByLocale($locale);
        $translation->setText($text);
        $translation->setFile($filename);
        return $this->stasher->persist($translation);
    }

    /**
     * Checks if the translation exists in the group for this locale, if not, it creates it
     */
    public function addTranslationIfNew(TranslationGroup $translationGroup, $locale, $text, $filename)
    {
        $translation = null;

        if ($translationGroup->hasTranslation($locale)) {
            return null;
        }

        $translation = new Translation;
        $translation->setLocale($locale);
        $translation->setText($text);
        $translation->setDomain($translationGroup->getTranslationDomain());
        $translation->setFile($filename);
        $translation->setKeyword($translationGroup->getKeyword());
        $translation->setCreatedAt(new \DateTime());
        $translation->setUpdatedAt(new \DateTime());

        $this->stasher->persist($translation);

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
}